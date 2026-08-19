#!/usr/bin/env bash

# This script will build the s3 assets and push them to the S3 bucket.
# This script is meant to be run locally (for testing/development) and not in the CI/CD pipeline.
# The CI/CD equivalent lives in .github/workflows/{build,deploy}.yml.

# Prerequisites:
# - Have the .env file in the project root.
# - Have the minio server running locally.

# Run the script from the project root with the following command:
# $ bin/local-run-s3-push.sh

set -e

# Load the environment variables from the .env file.
set -a && source .env && set +a

ASSETS_DIR="${TMPDIR:-/tmp}/intranet-s3-assets"
ALPINE="alpine:3.24@sha256:28bd5fe8b56d1bd048e5babf5b10710ebe0bae67db86916198a6eec434943f8b"

# Build the assets, then extract them - see the Dockerfile's build-s3-assets stage.
docker image build -t intranet-s3-assets:latest \
  --build-arg ACF_PRO_LICENSE --build-arg ACF_PRO_PASS   \
  --build-arg AS3CF_PRO_USER  --build-arg AS3CF_PRO_PASS \
  --target build-s3-assets .

rm -rf "$ASSETS_DIR"
mkdir -p "$ASSETS_DIR"

# `docker create` rejects an image with no command, so pass one. It is never run.
CID=$(docker create intranet-s3-assets:latest /noop)
trap 'docker rm "$CID" > /dev/null 2>&1 || true' EXIT

docker cp "$CID:/assets/." "$ASSETS_DIR/"

# Push the assets with a throwaway container, so that aws-cli and jq aren't needed on the host.
# A custom S3 endpoint is used because we are running minio locally.
docker run --rm \
  -e AWS_ACCESS_KEY_ID \
  -e AWS_SECRET_ACCESS_KEY \
  -e AWS_S3_BUCKET \
  -e IMAGE_TAG \
  -e AWS_ENDPOINT_URL=http://host.docker.internal:9000 \
  -v "$ASSETS_DIR":/assets \
  -v "$PWD/deploy/config/init/s3-push-start.sh":/usr/bin/s3-push-start:ro \
  -w /assets \
  "$ALPINE" \
  sh -c "apk add --no-cache aws-cli jq > /dev/null && sh /usr/bin/s3-push-start"
