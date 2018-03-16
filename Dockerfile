FROM php:7.2-fpm

# Keep composer happy
ENV COMPOSER_ALLOW_SUPERUSER 1
# Keep nullmailer from pestering for a name
ENV DEBIAN_FRONTEND noninteractive

# These need to be passed in at build time if you need to pull from the private MoJ composer repo at
# `https://composer.wp.dsd.io` and you are not on one of the approved networks. The required values can be found on
# `https://rattic.service.dsd.io` and can be passed in on the command line (for `docker`) or via environment variables
# (for `docker compose` - make sure you expose them in `build: -> args:`).
ARG COMPOSER_USER
ARG COMPOSER_PASS

EXPOSE 80

# If COMPOSER_USER and COMPOSER_PASS are set, this will create `/bedrock/auth.json`, which allows composer to fetch
# files from the MoJ private composer repo.
COPY setup_composer_auth.sh /usr/local/bin

# This script ensures that the database is ready and populated before allowing wordpress to start.
COPY wait-for-wordpress.sh /usr/local/bin

# Standard debian /etc config files should go here in a standard debian-esq structure.
COPY etc /etc/

# These are scripts that start services. They are called from supervisord. See
# https://github.com/ministryofjustice/intranet/blob/docker-refactor/etc/supervisor/supervisord.conf#L47
# for an example of how to configure one.
COPY services /services/

# These are scripts that perform one-off tasks at startup. They are called from supervisord. See
# https://github.com/ministryofjustice/intranet/blob/docker-refactor/etc/supervisor/supervisord.conf#L7
# for an example of how to configure one.
COPY runonce /runonce/

WORKDIR /bedrock

# Configuration, as well as themes and plugins that are not managed by composer should be installed from here.
COPY bedrock ./

# This is everything required for a 'standard' fpm-powered, nginx-fronted fat-container build.
RUN apt-get update \
  # Required for the apt-key add
  && apt-get install -y gnupg \
  && curl -sL https://deb.nodesource.com/setup_9.x | bash - \
  && echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' > /etc/apt/sources.list.d/newrelic.list \
  && curl -fsSL https://download.newrelic.com/548C16BF.gpg | apt-key add - \
  && echo 'deb http://nginx.org/packages/mainline/debian/ stretch nginx deb-src http://nginx.org/packages/mainline/debian/ stretch nginx' > /etc/apt/sources.list.d/nginx.list \
  && curl -fsSL http://nginx.org/keys/nginx_signing.key | apt-key add - \
  && apt-get update \
  && apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" -y install \
  # Required for yas3fs
  fuse \
  git \
  libffi-dev \
  mariadb-client \
  newrelic-php5 \
  nginx \
  nullmailer \
  nodejs \
  python-pip \
  ruby-dev \
  sass \
  supervisor \
  && apt-get clean \
  && docker-php-ext-install mysqli pdo pdo_mysql \
  # Install composer
  && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && php -r "unlink('composer-setup.php');" \
  # Enable access to S3 as a filesystem
  && pip install yas3fs \
  # Required for yas3fs
  && mkdir -p web/app/uploads \
  && setup_composer_auth.sh \
  && composer install --verbose \
  && rm composer.json \
  && rm bedrock.json \
  && rm moj.json \
  && rm composer.lock

# The following two RUN commands are specific to the setup of the MoJ intranet.
# This one is for the legacy theme
RUN npm install --global grunt-cli \
  && npm install \
  && grunt pre_deploy \
  && rm Gruntfile.js

# This one is for the clarity theme
RUN cd /bedrock/web/app/themes/intranet-theme-clarity \
  && npm install --global gulp-cli \
  && npm install \
  && gulp build \
  && cd /bedrock \
  && rm -rf node_modules \
  && rm package.json \
  && rm package-lock.json

WORKDIR /

CMD ["wait-for-wordpress.sh"]
