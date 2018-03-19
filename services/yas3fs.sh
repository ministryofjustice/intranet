#!/bin/bash

set -e

if [ ! -z "$SNS_TOPIC" ]
then
	exec /usr/local/bin/yas3fs \
    --foreground \
    --new-queue-with-hostname \
    --no-metadata \
    --nonempty "s3://$AWS_S3_BUCKET/uploads" /bedrock/web/app/uploads \
    --region "$AWS_DEFAULT_REGION" \
    --s3-endpoint "s3-$AWS_DEFAULT_REGION.amazonaws.com" \
    --s3-use-sigv4 \
    --topic "$SNS_TOPIC"
else
  exec /usr/local/bin/yas3fs \
    --foreground \
    --no-metadata \
    --nonempty "s3://$AWS_S3_BUCKET/uploads" /bedrock/web/app/uploads \
    --region "${AWS_DEFAULT_REGION:='eu-west-1'}" \
    --s3-use-sigv4 \
    --s3-endpoint "s3-$AWS_DEFAULT_REGION.amazonaws.com"
fi
