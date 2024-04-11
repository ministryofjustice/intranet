#!/usr/bin/env bash

# This script creates a JWT sectet, RSA key pair and copies them to the clipboard - ready for pasting into .env.
# The script follows the instructions from the AWS 'Creating key pairs for your signers' documentation:
# https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/private-content-trusted-signers.html#private-content-creating-cloudfront-key-pairs

echo "Generating JWT secret"
JWT_SECRET=$(openssl rand -base64 64)

echo "Generating RSA keys"
echo "writing RSA private key"
openssl genrsa -out /tmp/intranet_private_key.pem 2048

openssl rsa -pubout -in /tmp/intranet_private_key.pem -out /tmp/intranet_public_key.pem

AWS_CLOUDFRONT_PUBLIC_KEY=$(cat /tmp/intranet_public_key.pem)
# Substring of public key starting at 72 chars and 8 chars long.
AWS_CLOUDFRONT_PUBLIC_KEY_OBJECT="[{\"id\":\"GENERATED_BY_AWS\",\"key\":\"$(echo $AWS_CLOUDFRONT_PUBLIC_KEY | cut -c 72-79)\"}]"
AWS_CLOUDFRONT_PRIVATE_KEY=$(cat /tmp/intranet_private_key.pem)

echo "Keys copied to clipboard"
echo -e "JWT_SECRET=\"$JWT_SECRET\"\n\nAWS_CLOUDFRONT_PUBLIC_KEY_OBJECT=$AWS_CLOUDFRONT_PUBLIC_KEY_OBJECT\n\nAWS_CLOUDFRONT_PUBLIC_KEY=\"$AWS_CLOUDFRONT_PUBLIC_KEY\"\n\nAWS_CLOUDFRONT_PRIVATE_KEY=\"$AWS_CLOUDFRONT_PRIVATE_KEY\"" | pbcopy

# Clear the variables.
unset JWT_SECRET
unset AWS_CLOUDFRONT_PUBLIC_KEY
unset AWS_CLOUDFRONT_PRIVATE_KEY
unset AWS_CLOUDFRONT_PUBLIC_KEY_OBJECT

echo "Deleting temporary key files"
rm /tmp/intranet_private_key.pem /tmp/intranet_public_key.pem
