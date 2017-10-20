FROM ruby:alpine

ENV PHANTOM_VERSION 2.1.1
ENV TARGET_URI http://intranet.docker

ADD . /
WORKDIR /

RUN apk add --update \
    build-base \
    bash \
    curl \
    docker && \
    bundle install

CMD ./wait-for-wordpress.sh ${TARGET_URI}
