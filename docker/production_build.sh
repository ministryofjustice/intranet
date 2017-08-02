#!/bin/sh

set -e

if [ ! -z $production ]
then
  exec ./build.sh
fi
