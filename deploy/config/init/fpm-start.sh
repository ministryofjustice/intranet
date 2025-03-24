#!/bin/sh

if wp core is-installed 2>/dev/null; then
    # WP is installed.
    wp sync-user-roles sync

    # Start temporary code to ensure that one of the Amazon S3 and Cloudfront plugins is activated.
    # This is a temporary solution until wp-amazon-s3-and-cloudfront passes QA, and then we can
    # move it to mu-plugins.

    AS3C_IS_ACTIVATED=false
    AS3C_IS_INSTALLED=false
    AS3CP_IS_INSTALLED=false
    AS3CP_IS_ACTIVATED=false
    AS3C_PLUGIN_TO_ACTIVATE=false

    # Check if wp-amazon-s3-and-cloudfront plugin is installed, update the AS3C_PLUGIN_TO_ACTIVATE variable accordingly.
    if wp plugin is-installed wp-amazon-s3-and-cloudfront 2>/dev/null; then
        AS3C_PLUGIN_TO_ACTIVATE=wp-amazon-s3-and-cloudfront
        echo 'wp-amazon-s3-and-cloudfront plugin is installed.'
    fi

    # Check if wp-amazon-s3-and-cloudfront plugin is activated and set it as a variable: AS3C_IS_ACTIVATED
    if wp plugin is-active wp-amazon-s3-and-cloudfront 2>/dev/null; then
        AS3C_IS_ACTIVATED=true
        echo 'wp-amazon-s3-and-cloudfront plugin is activated.'
    fi

    # Check if amazon-s3-and-cloudfront-pro plugin is installed, update the AS3C_PLUGIN_TO_ACTIVATE variable accordingly.
    if wp plugin is-installed amazon-s3-and-cloudfront-pro 2>/dev/null; then
        AS3C_PLUGIN_TO_ACTIVATE=amazon-s3-and-cloudfront-pro
        echo 'amazon-s3-and-cloudfront-pro plugin is installed.'
    fi

    # Check if amazon-s3-and-cloudfront-pro plugin is activated and set it as a variable. AS3CP_IS_ACTIVATED
    if wp plugin is-active amazon-s3-and-cloudfront-pro 2>/dev/null; then
        AS3CP_IS_ACTIVATED=true
        echo 'amazon-s3-and-cloudfront-pro plugin is activated.'
    fi

    # If both AS3CP_IS_ACTIVATED and AS3C_IS_ACTIVATED are false, then activate the desired plugin.
    if [ "$AS3CP_IS_ACTIVATED" = false ] && [ "$AS3C_IS_ACTIVATED" = false ]; then
        wp plugin activate $AS3C_PLUGIN_TO_ACTIVATE
        echo "$AS3C_PLUGIN_TO_ACTIVATE plugin is activated."
    fi

    # End temporary code to ensure that one of the Amazon S3 and Cloudfront plugins is activated.

else
    # Fallback if WP is not installed.
    # This will happen during a first run on localhost.
    echo 'WordPress is not installed yet, so skipping command `wp sync-user-roles sync` in `fpm-start.sh`.'
fi
