#!/bin/sh

# Disable the yas3fs service if AWS_S3_BUCKET environment variable has not been set.

if [ -z "$AWS_S3_BUCKET" ]
then
	touch "/etc/service/yas3fs/down"
fi
