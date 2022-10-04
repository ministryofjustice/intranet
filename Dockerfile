FROM ministryofjustice/wordpress-base:php8.1

ADD . /bedrock

WORKDIR /bedrock

ARG COMPOSER_USER
ARG COMPOSER_PASS

# Add custom nginx config and init script
# the last command in this run sequence removes the whitelist IP configuration file - see README for info
RUN mv docker/conf/nginx/server.conf /etc/nginx/sites-available/ && \
    mv docker/conf/nginx/php-fpm.conf /etc/nginx/ && \
    mv docker/conf/php-fpm/php-fpm.conf /etc/php/8.1/fpm && \
    mv docker/conf/php-fpm/pool.conf /etc/php/8.1/fpm/pool.d && \
    mv docker/init/configure-maintenance-mode.sh /etc/my_init.d/ && \
    chmod +x /etc/my_init.d/configure-maintenance-mode.sh && \
    rm -f /etc/my_init.d/configure-ip-whitelist.sh

# Set execute bit permissions before running build scripts
RUN chmod +x bin/* && sleep 1 && \
    #make clean && \
    bin/composer-auth.sh && \
    make build && \
    rm -f auth.json
