#!/bin/sh

# This script is executed when the FPM container is stopped.

if wp core is-installed 2>/dev/null; then
    # WP is installed.

    # Deregister the current container/pod from the cluster.
    wp cluster-helper deregister-self
fi
