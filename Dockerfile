FROM --platform=linux/amd64 ministryofjustice/intranet-base:latest

ADD . /bedrock
WORKDIR /bedrock

ARG COMPOSER_USER
ARG COMPOSER_PASS

# Add custom nginx config and init script
RUN mv docker/conf/nginx/server.conf /etc/nginx/sites-available/

# Set execute bit permissions before running build scripts
RUN chmod +x bin/* && sleep 1 && \
    #make clean && \
    bin/composer-auth.sh && \
    make build && \
    rm -f auth.json
