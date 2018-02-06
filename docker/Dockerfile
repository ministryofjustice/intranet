FROM mojdigital/wordpress-base:latest

ARG production
ARG COMPOSER_USER
ARG COMPOSER_PASS

# Stop the ubuntu phatomjs package breaking on install (qt wants an X window
# server connection).
ENV QT_QPA_PLATFORM=offscreen
ENV COMPOSER_ALLOW_SUPERUSER 1

ADD . /

WORKDIR /

# Grunt requires ruby and sass.  Two packages installed by grunt require
# phantomjs (not sure which ones yet).  This satsifies *one* of those
# dependencies (for phantom ~= 2.1.x). The older one (1.9.x) is installed by
# grunt.
## ## ## ## ## ##
# TODO: review these dependencies so everything uses the ubuntu packaged
# 2.1.x version of phantom.
RUN apt-get update && apt-get install -y ruby ruby-dev phantomjs libffi-dev
RUN gem install sass
RUN ln -sf /dev/stdout /var/log/fpm.slow.log

# Run ./build.sh as part of the build process IFF we are running in production
# mode (set in `docker-compose.yml`), otherwise defer until runtime
RUN chmod +x production_build.sh build.sh
RUN ./production_build.sh
