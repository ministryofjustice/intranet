FROM mojdigital/wordpress-base:latest

# Stop the ubuntu phatomjs package breaking on install (qt wants an X window
# server connection).
ENV QT_QPA_PLATFORM=offscreen

ADD . /bedrock

WORKDIR /bedrock

RUN composer install --verbose && \
  rm composer.json && \
  rm composer.lock && \
  rm bedrock.json && \
  rm moj.json && \
  # Because composer cannot install this in the correct location and does not
  # seem to be able to easily move it, itself.
  mv vendor/ministryofjustice/mojintranet-theme/wp-content/themes/mojintranet web/app/themes/ && \
  rm -rf vendor/ministryofjustice

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
  npm install -g grunt-cli && \
  npm install && \
  grunt pre_deploy && \
  rm -rf node_modules Gruntfile.js package.json
