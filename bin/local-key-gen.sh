#!/usr/bin/env bash

# This script creates a JWT secret, RSA key pair and saves them into .env.
# If the secrets already exist in the .env file, the script will not overwrite them.
# The script follows the instructions from the AWS 'Creating key pairs for your signers' documentation:
# https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/private-content-trusted-signers.html#private-content-creating-cloudfront-key-pairs

source bin/local-key-gen-functions.sh

ENV_FILE=".env"
FILE_OUTPUT="/tmp/intranet_secrets_string"

# Create outputs file
touch $FILE_OUTPUT
{
  echo -e "\n# # # # # # # # # # # # # # # # # #"
  echo "# -->  auto-gen secrets keys  <-- #"
  echo "# # # # # # # # # # # # # # # # # #"
} > $FILE_OUTPUT

[[ "$(env_var_exists JWT_SECRET)" == "0" ]] && make_secret JWT
[[ "$(env_var_exists AWS_CLOUDFRONT_PRIVATE_KEY)" == "0" ]] && make_secret PRIVATE_KEY
[[ "$(env_var_exists AWS_CLOUDFRONT_PUBLIC_KEY)" == "0" ]] && make_secret PUBLIC_KEY
[[ "$(env_var_exists AWS_CLOUDFRONT_PUBLIC_KEYS_OBJECT)" == "0" ]] && make_secret PUBLIC_KEYS_OBJECT

if [[ "$(action_track)" == "0" ]]; then
  echo "No new secrets were created."
  clean_up quiet
  exit 0
fi

# Append secrets to the .env file
cat $FILE_OUTPUT >> $ENV_FILE

# Clear the variables.
clean_up
