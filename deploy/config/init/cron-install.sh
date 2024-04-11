#!/bin/sh

# Supercronic
# SHAs from the releases page: https://github.com/aptible/supercronic/releases

set -eux; 

SUPERCRONIC_VERSION='v0.2.29';
    
dpkgArch="$(dpkg --print-architecture)";

case "${dpkgArch##*-}" in
  amd64) arch='x86_64' ; supercronic='supercronic-linux-amd64' ; supercronic_sha='cd48d45c4b10f3f0bfdd3a57d054cd05ac96812b' ;;
  armhf) arch='aarch64' ; supercronic='supercronic-linux-arm64' ; supercronic_sha='512f6736450c56555e01b363144c3c9d23abed4c' ;;
  arm64) arch='aarch64' ; supercronic='supercronic-linux-arm64' ; supercronic_sha='512f6736450c56555e01b363144c3c9d23abed4c' ;;
  *) arch='unimplemented' ;
    echo >&2; echo >&2 "warning: current architecture ($dpkgArch) does not have a corresponding binary release."; echo >&2 ;;
esac;

if [ "$arch" = 'unimplemented' ]; then
    echo >&2;
    echo >&2 'error: UNIMPLEMENTED CRON';
    echo >&2 'TODO install supercronic';
    echo >&2;
    exit 1;
fi;

wget --quiet "https://github.com/aptible/supercronic/releases/download/${SUPERCRONIC_VERSION}/${supercronic}" &&
echo "${supercronic_sha}  ${supercronic}" | sha1sum -c - &&
chmod +x "${supercronic}" &&
mv "${supercronic}" "/usr/local/bin/${supercronic}" &&
ln -s "/usr/local/bin/${supercronic}" /usr/local/bin/supercronic
