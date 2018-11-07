FROM mojdigital/wordpress-base:latest

ARG production
ARG COMPOSER_USER
ARG COMPOSER_PASS

# Stop the ubuntu phatomjs package breaking on install (qt wants an X window
# server connection).
ENV QT_QPA_PLATFORM=offscreen
ENV COMPOSER_ALLOW_SUPERUSER 1
# This is to ensure new relic builds non-interactively
ENV DEBIAN_FRONTEND=noninteractive

ADD . /

WORKDIR /

RUN echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' > /etc/apt/sources.list.d/newrelic.list \
      && curl -fsSL https://download.newrelic.com/548C16BF.gpg | apt-key add - \
      && apt-get update \
      && apt-get install -y libffi-dev newrelic-php5 \
      && ln -sf /dev/stdout /var/log/fpm.slow.log \
      # Run ./build.sh as part of the build process IFF we are running in production
      # mode (set in `docker-compose.yml`), otherwise defer until runtime
      && chmod +x production_build.sh build.sh \
      && ./production_build.sh
