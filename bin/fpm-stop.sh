#!/bin/sh

# This script is executed when the FPM container is stopped.

# Deregister the current container/pod from the cluster.
wp cluster-helper deregister-self --skip-plugins --skip-themes
