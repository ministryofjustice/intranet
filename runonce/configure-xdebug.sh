#!/bin/sh

# Enable XDebug if XDEBUG_CONFIG environment variable exists
# XDebug will automatically read config from XDEBUG_CONFIG, so all we need to do here is enable it.

if [ ! -z "$XDEBUG_CONFIG" ]
then
	phpenmod xdebug
fi
