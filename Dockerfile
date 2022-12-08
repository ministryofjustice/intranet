FROM ministryofjustice/wordpress-base:latest

ADD . /bedrock

WORKDIR /bedrock

ARG COMPOSER_USER
ARG COMPOSER_PASS

# Add custom nginx config and init script
# the last command in this run sequence removes the whitelist IP configuration file - see README for info
RUN sed -i 's/fastcgi_intercept_errors off;/fastcgi_intercept_errors on;/' /etc/nginx/php-fpm.conf && \
    echo "\nfastcgi_buffers 16 16k;\nfastcgi_buffer_size 32k;" >> /etc/nginx/php-fpm.conf && \
    echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' > /etc/apt/sources.list.d/newrelic.list && \
    curl -fsSL https://download.newrelic.com/548C16BF.gpg | apt-key add - && \
    mv docker/conf/nginx/server.conf /etc/nginx/sites-available/ && \
    mv docker/conf/php-fpm/newrelic.ini /etc/php/7.4/fpm/conf.d/ && \
    mv docker/init/configure-maintenance-mode.sh /etc/my_init.d/ && \
    chmod +x /etc/my_init.d/configure-maintenance-mode.sh && \
    apt-get update && \
    apt-get install -y libffi-dev newrelic-php5 && \
    rm -f /etc/my_init.d/configure-ip-whitelist.sh

# Set execute bit permissions before running build scripts
RUN chmod +x bin/* && sleep 1 && \
    #make clean && \
    bin/composer-auth.sh && \
    make build && \
    rm -f auth.json
