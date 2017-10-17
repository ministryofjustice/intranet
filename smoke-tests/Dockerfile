FROM ruby:2.4.1-onbuild

ENV PHANTOM_VERSION 2.1.1

WORKDIR /

# The phantomjs gem will download phantom automatically at runtime. However,
# doing so is slow and can cause intermittent failures if there are network
# issues.  This ensures an up-to-date version is already in place at runtime.
# The version is debian backports does not work with this version of the gem.
# TODO: commented out for now as it seems to be failing to download !!
#RUN wget -q https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-${PHANTOM_VERSION}-linux-x86_64.tar.bz2 && \
#  bunzip2 phantomjs-${PHANTOM_VERSION}-linux-x86_64.tar.bz2 && \
#  tar xf phantomjs-${PHANTOM_VERSION}-linux-x86_64.tar && \
#  mv phantomjs-${PHANTOM_VERSION}-linux-x86_64/bin/phantomjs /usr/local/bin && \
#  rm -rf phantomjs*

ADD Gemfile /
ADD config config
ADD features features

# TODO: commented out so smoke tests doesn't run automatically. Run manually with:
# docker-compose -f docker-compose-dev.yml run smoketests cucumber -c
#CMD cucumber -c
