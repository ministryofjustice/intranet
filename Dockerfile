FROM php:7.2-fpm

# Set system locale
ENV LC_ALL="en_GB.UTF-8" \
 LANG="en_GB.UTF-8" \
 LANGUAGE="en_GB.UTF-8"

# Phantom crashes frequently without this
ENV QT_QPA_PLATFORM=offscreen
# Keep composer happy
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /

RUN apt-get update \
  && apt-get install -y gnupg \
  && curl -sL https://deb.nodesource.com/setup_9.x | bash - \
  && echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' > /etc/apt/sources.list.d/newrelic.list \
  && curl -fsSL https://download.newrelic.com/548C16BF.gpg | apt-key add - \
  && apt-get update \
  && apt-get install -y \
  fuse \
  git \
  gnupg \
  libffi-dev \
  mariadb-client \
  newrelic-php5 \
  nginx \
  nodejs \
  python-pip \
  ruby-dev \
  sass \
  && apt-get clean \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && php -r "unlink('composer-setup.php');" \
  && pip install yas3fs

WORKDIR /bedrock
ADD composer.json .
ADD bedrock.json .
ADD moj.json .

ADD Gruntfile.js .
ADD package.json .

ADD mojintranet web/app/themes/mojintranet/

RUN mkdir -p web/app/uploads \
  && composer install --verbose \
  && rm bedrock.json \
  && rm composer.json \
  && rm moj.json \
  && rm composer.lock

RUN npm install --global grunt-cli \
  && npm install \
  && grunt pre_deploy

RUN cd /bedrock/web/app/themes/intranet-theme-clarity \
  && npm install --global gulp-cli \
  && npm install \
  && gulp build \
&& cd /bedrock

WORKDIR /
RUN rm /etc/nginx/sites-enabled/default
ADD /etc .
