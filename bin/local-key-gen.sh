#!/usr/bin/env bash

# This script creates a RSA key pair and copies the public key to the clipboard.
# Follows the instructions from the AWS 'Creating key pairs for your signers' documentation:
# https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/private-content-trusted-signers.html#private-content-creating-cloudfront-key-pairs

echo "Generating RSA keys"
echo "writing RSA private key"
openssl genrsa -out /tmp/intranet_private_key.pem 2048

openssl rsa -pubout -in /tmp/intranet_private_key.pem -out /tmp/intranet_public_key.pem

echo "Keys copied to clipboard"
echo -e "CLOUDFRONT_PUBLIC_KEY=\"$(cat /tmp/intranet_public_key.pem)\"\n\nCLOUDFRONT_PRIVATE_KEY=\"$(cat /tmp/intranet_private_key.pem)\"" | pbcopy

echo "Deleting temporary key files"
rm /tmp/intranet_private_key.pem /tmp/intranet_public_key.pem
