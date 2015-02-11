FROM wordpress:4

COPY . /var/www/html/
COPY docker/docker-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
