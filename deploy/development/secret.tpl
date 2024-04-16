apiVersion: v1
kind: Secret
metadata:
  name: intranet-dev-secrets
type: Opaque
stringData:
  GOV_NOTIFY_API_KEY: "${GOV_NOTIFY_API_KEY}"
  SENTRY_DSN: "${SENTRY_DSN}"
  AUTH_KEY: "${AUTH_KEY}"
  AUTH_SALT: "${AUTH_SALT}"
  LOGGED_IN_KEY: "${LOGGED_IN_KEY}"
  LOGGED_IN_SALT: "${LOGGED_IN_SALT}"
  NONCE_KEY: "${NONCE_KEY}"
  NONCE_SALT: "${NONCE_SALT}"
  SECURE_AUTH_KEY: "${SECURE_AUTH_KEY}"
  SECURE_AUTH_SALT: "${SECURE_AUTH_SALT}"
  JWT_SECRET: "${JWT_SECRET}"
---
apiVersion: v1
kind: Secret
metadata:
  name: intranet-dev-base64-secrets
type: Opaque
data:
  AWS_CLOUDFRONT_PRIVATE_KEY: "${AWS_CLOUDFRONT_PRIVATE_KEY_BASE64}"
---
apiVersion: v1
kind: Secret
metadata:
  name: cloudfront-input
type: Opaque
data:
  AWS_CLOUDFRONT_PUBLIC_KEY: "${AWS_CLOUDFRONT_PUBLIC_KEY_BASE64}"
  # AWS_CLOUDFRONT_PUBLIC_KEY_EXPIRING: "${AWS_CLOUDFRONT_PUBLIC_KEY_EXPIRING_BASE64}"
