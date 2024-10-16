#!/bin/sh

echo "Starting to psh assets to s3"

# The s3 bucket is AWS_S3_BUCKET env var.
# The s3 destination folder is IMAGE_TAG env var.

echo "Will push assets to s3://$AWS_S3_BUCKET/$IMAGE_TAG"
