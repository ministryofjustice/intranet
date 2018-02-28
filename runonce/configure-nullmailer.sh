#!/bin/sh

# Create remotes configuration for nullmailer using environment variables

REMOTES="$SMTP_HOST smtp"

if [ ! -z "$SMTP_USER" ]
then
	REMOTES="$REMOTES --user=$SMTP_USER"
fi

if [ ! -z "$SMTP_PASS" ]
then
	REMOTES="$REMOTES --pass=$SMTP_PASS"
fi

if [ ! -z "$SMTP_PORT" ]
then
	REMOTES="$REMOTES --port=$SMTP_PORT"
fi

if [ "$SMTP_USE_STARTTLS" = true ]
then
	REMOTES="$REMOTES --starttls"
fi

if [ "$SMTP_USE_SSL" = true ]
then
	REMOTES="$REMOTES --ssl"
fi

echo "$REMOTES" > /etc/nullmailer/remotes
