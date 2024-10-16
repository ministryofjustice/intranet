#!/usr/bin/env bash

ACTION_TRACKER="/tmp/intranet_action_tracker"
FILE_PRIVATE="/tmp/intranet_private_key.pem"
FILE_PUBLIC="/tmp/intranet_public_key.pem"

ENV_FILE=".env"
FILE_OUTPUT="/tmp/intranet_secrets_string"

# Create outputs files
touch $FILE_OUTPUT
{
  echo -e "\n# # # # # # # # # # # # # # # # # #"
  echo "# -->  auto-gen secrets keys  <-- #"
  echo "# # # # # # # # # # # # # # # # # #"
} > $FILE_OUTPUT

env_var_exists(){
  VAR=$(< "$ENV_FILE" grep -w "$1")
  VALUE=${VAR#*=}
  VALUE_SIZE=${#VALUE}

  if [[ $VALUE_SIZE -gt 25 ]] ; then
      echo "$1 exists with a value"
      echo "$VALUE"
  else
      echo "0"
  fi
}

touch $ACTION_TRACKER
action_track(){
  TRACKER_SIZE=$(sed -n '$='  "$ACTION_TRACKER")
  if [[ "$TRACKER_SIZE" -gt 1 ]] ; then
      echo "1"
  else
      echo "0"
  fi
}

make_secret(){
  case $1 in

    JWT)
      echo "Generating JWT secret"
      ## append to file
      echo -e "JWT_SECRET=$(openssl rand -base64 64 | tr -d '\n')\n" >> "$FILE_OUTPUT"
      echo "JWT created" >> "$ACTION_TRACKER"
      ;;

    PUBLIC_KEY)
      echo "Generating RSA public key"
      openssl rsa -pubout -in "$FILE_PRIVATE" -out "$FILE_PUBLIC"
      AWS_CLOUDFRONT_PUBLIC_KEY=$(cat "$FILE_PUBLIC")
      ## append to file
      echo -e "AWS_CLOUDFRONT_PUBLIC_KEY=\"$AWS_CLOUDFRONT_PUBLIC_KEY\"\n" >> "$FILE_OUTPUT"
      AWS_CLOUDFRONT_PUBLIC_KEY_SHORT_HASH="$(echo "$AWS_CLOUDFRONT_PUBLIC_KEY" | openssl dgst -binary -sha256 | xxd -p -c 32 | cut -c 1-8)"
      echo "Public key created" >> "$ACTION_TRACKER"
      ;;

    PRIVATE_KEY)
      echo "Generating RSA private key"
      openssl genrsa -out "$FILE_PRIVATE" 2048
      ## append to file
      echo -e "AWS_CLOUDFRONT_PRIVATE_KEY=\"$(cat "$FILE_PRIVATE")\"\n" >> "$FILE_OUTPUT"
      echo "Private key created" >> "$ACTION_TRACKER"
      ;;

    PUBLIC_KEYS_OBJECT)
      echo "Generating public keys object"
      ## append to file
      echo -e AWS_CLOUDFRONT_PUBLIC_KEYS_OBJECT="[{\"id\":\"GENERATED_BY_AWS\",\"comment\":\"$AWS_CLOUDFRONT_PUBLIC_KEY_SHORT_HASH\"}]\n" >> "$FILE_OUTPUT"
      echo "Public keys object created" >> "$ACTION_TRACKER"
      ;;
  esac
}

clean_up(){
  unset AWS_CLOUDFRONT_PUBLIC_KEY
  unset AWS_CLOUDFRONT_PUBLIC_KEY_SHORT_HASH

  [[ -f "$ACTION_TRACKER" ]] && rm "$ACTION_TRACKER"
  [[ -f "$FILE_PRIVATE" ]] && rm "$FILE_PRIVATE"
  [[ -f "$FILE_PUBLIC" ]] && rm "$FILE_PUBLIC"
  [[ -f "$FILE_OUTPUT" ]] && rm "$FILE_OUTPUT"
}
