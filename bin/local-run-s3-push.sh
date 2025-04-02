#!/usr/bin/env bash

# This script will build an s3-push image and run the container.
# When the container runs, assets will be pushed to the S3 bucket.
# This script is meant to be run locally (for testing/development) and not in the CI/CD pipeline.

# Prerequisites:
# - Have the .env file in the project root.
# - Have the minio server running locally.

# Run the script from the project root with the following command:
# $ bin/local-run-s3-push.sh

# Load the environment variables from the .env file.
set -a && source .env && set +a

docker image build -t intranet-s3-push:latest \
  --build-arg ACF_PRO_LICENSE --build-arg ACF_PRO_PASS   \
  --build-arg AS3CF_PRO_USER  --build-arg AS3CF_PRO_PASS \
  --build-arg IMAGE_TAG \
  --target build-s3-push .

# Run the container with env vars from .env, 
# and a custom S3 endpoint because we are using minio locally.
docker run --rm -it \
  -e AWS_ACCESS_KEY_ID \
  -e AWS_SECRET_ACCESS_KEY \
  -e AWS_S3_BUCKET \
  -e AWS_ENDPOINT_URL=http://host.docker.internal:9000 \
  intranet-s3-push:latest
