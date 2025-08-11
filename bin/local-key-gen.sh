#!/usr/bin/env bash

# This script creates a JWT secret, RSA key pair and saves them into .env.
# If the secrets already exist in the .env file, the script will not overwrite them.
# The script follows the instructions from the AWS 'Creating key pairs for your signers' documentation:
# https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/private-content-trusted-signers.html#private-content-creating-cloudfront-key-pairs

echo "Key Generation: detection..."
source bin/local-key-gen-functions.sh

[[ "$(env_var_exists JWT_SECRET)" == "0" ]] && make_secret JWT
[[ "$(env_var_exists INTRANET_ARCHIVE_SHARED_SECRET)" == "0" ]] && make_secret INTRANET_ARCHIVE
[[ "$(env_var_exists AWS_CLOUDFRONT_PRIVATE_KEY)" == "0" ]] && make_secret PRIVATE_KEY
[[ "$(env_var_exists AWS_CLOUDFRONT_PUBLIC_KEY)" == "0" ]] && make_secret PUBLIC_KEY
[[ "$(env_var_exists AWS_CLOUDFRONT_PUBLIC_KEYS_OBJECT)" == "0" ]] && make_secret PUBLIC_KEYS_OBJECT

if [[ "$(action_track)" == "0" ]]; then
  echo "Key Generation: no new keys were created."
  clean_up quiet
  exit 0
fi

# Append secrets to the .env file
cat "$FILE_OUTPUT" >> "$ENV_FILE"
echo "Key Generation: new keys were created."

# Clear the variables.
clean_up
