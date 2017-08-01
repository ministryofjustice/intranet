FROM mojdigital/wordpress-base:latest

# Stop the ubuntu phatomjs package breaking on install (qt wants an X window
# server connection).
ENV QT_QPA_PLATFORM=offscreen
ENV COMPOSER_ALLOW_SUPERUSER 1

ADD . /bedrock

WORKDIR /bedrock

RUN apt-get update && \
  # Grunt requires ruby and sass.  Two packages installed by grunt require
  # phantomjs (not sure which ones yet).  This satsifies *one* of those
  # dependencies (for phantom ~= 2.1.x). The older one (1.9.x) is installed by
  # grunt.
  ## ## ## ## ## ##
  # TODO: review these dependencies so everything uses the ubuntu packaged
  # 2.1.x version of phantom.
  apt-get install -y ruby ruby-dev phantomjs && \
  gem install sass && \
  ./build.sh

# Enure the image doesn't get too large (node_modules is probably the worst offender).
RUN rm composer.json && \
  rm composer.lock && \
  rm bedrock.json && \
  rm moj.json && \
  rm -rf node_modules Gruntfile.js package.json
