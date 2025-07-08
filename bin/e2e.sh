#!/usr/bin/env bash

# Echo so we know this is running
echo "Running e2e.sh"

docker compose up -d minio

# Wait for MinIO container to be healthy
while ! docker compose exec minio mc ls minio; do
  echo "Waiting for MinIO to be ready..."
  sleep 0.1
done

docker compose up minio-init --exit-code-from minio-init

# Check the exit code was 0;
if [ $? -ne 0 ]; then
  echo "MinIO initialization failed"
  exit 1
fi

echo "MinIO initialization succeeded"


# Start the e2e tests
echo "Starting e2e tests"

docker compose --env-file .env.e2e -f docker-compose.yml -f docker-compose.e2e.yml up \
  --build \
  --abort-on-container-exit \
  --exit-code-from e2e \
  --attach e2e

# echo the exit code of the e2e tests
if [ $? -ne 0 ]; then
    echo "E2E tests failed"
    exit 1
else
    echo "E2E tests passed"
fi

# Finally stop minio and minio-init
docker compose stop
