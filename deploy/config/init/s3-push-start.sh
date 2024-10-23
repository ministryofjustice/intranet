#!/bin/sh

export AWS_CLI_ARGS=""
# Truncate $IMAGE_TAG to 8 chars.
export IMAGE_TAG=$(echo $IMAGE_TAG | cut -c1-8)
export S3_DESTINATION="s3://$AWS_S3_BUCKET/build/$IMAGE_TAG"
export S3_MANIFEST="s3://$AWS_S3_BUCKET/build/manifests/$IMAGE_TAG.json"
export S3_SUMMARY="s3://$AWS_S3_BUCKET/build/manifests/summary.jsonl"
export TIMESTAMP=$(date +%s)


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 1️⃣ Function to handle errors
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

# Accepts 2 arguments, the return code of the aws command and the command itself.
# 0: The service responded with an HTTP response status code of 200 and there were 
#    no errors from either the CLI or the service the request was made to.
# 1: At least one or more s3 transfers failed for the command executed.
# 2: The meaning of this return code depends on the command being run.

catch_error() {
  if [ $1 -ne 0 ]; then
    echo "Error: command \`$2\` failed with return code $1"
    exit $1
  fi
}


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 2️⃣ Prepare CLI arguments
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

# If $AWS_ENDPOINT_URL is set and it's not an empty string, append to the AWS CLI args.
# This allows for localhost testing with minio.
if [ -n "$AWS_ENDPOINT_URL" ]; then
  export AWS_CLI_ARGS="$AWS_CLI_ARGS --endpoint-url $AWS_ENDPOINT_URL"
fi


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 3️⃣ Sync files to S3
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

echo "Syncing assets to $S3_DESTINATION ..."

# Sync the contents of the static folder to the s3 bucket
aws $AWS_CLI_ARGS s3 sync ./public $S3_DESTINATION
catch_error $? "aws s3 sync ./public $S3_DESTINATION"


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 4️⃣ Get a list of uploaded files
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

echo "Fetching list of uploaded files..."

# Get an array of all the files that were uploaded, remove "/build/$IMAGE_TAG" from the start.
UPLOADED_FILES=$(aws $AWS_CLI_ARGS s3 ls $S3_DESTINATION/ --recursive | awk '{print $4}')
catch_error $? "aws s3 ls $S3_DESTINATION/ --recursive"


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 5️⃣ Verify file counts
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

echo "Verifying file counts..."

# Verify that the number of files uploaded matches the number of files in the public folder.
# This is in addition to s3 sync's built-in verification.
LOCAL_FILES_COUNT=$(find ./public -type f | wc -l)
UPLOADED_FILES_COUNT=$(echo "$UPLOADED_FILES" | wc -l)

if [ $LOCAL_FILES_COUNT -ne $UPLOADED_FILES_COUNT ]; then
  echo "Error: The number of uploaded files ($UPLOADED_FILES_COUNT) does not match the number of local files ($LOCAL_FILES_COUNT)"
  exit 1
fi


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 6️⃣ Copy the list of uploaded files to S3
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

echo "Copying manifest to $S3_MANIFEST..."

# Use jq to parse the line-seperated $UPLOADED_FILES variable into a json array.
echo "$UPLOADED_FILES" | jq -R -s '{timestamp: '$TIMESTAMP', build: "'$IMAGE_TAG'", files: split("\n")[:-1]}' > ./mainfest.json

aws $AWS_CLI_ARGS s3 cp ./mainfest.json $S3_MANIFEST
catch_error $? "aws s3 cp ./mainfest.json $S3_MANIFEST"


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
# 7️⃣ Append this manifest to the summary
# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

echo "Getting summary file..."

SUMMARY_RESPONSE=$(aws $AWS_CLI_ARGS s3 ls $S3_SUMMARY)
catch_error $? "aws s3 ls $S3_SUMMARY"

if [ -n "$SUMMARY_RESPONSE" ]; then
  echo "Summary file exists. Downloading..."
  aws $AWS_CLI_ARGS s3 cp $S3_SUMMARY ./summary.jsonl
  catch_error $? "aws s3 cp $S3_SUMMARY ./summary.jsonl"
else
  echo "Summary file does not exist. Creating..."
  touch ./summary.jsonl
fi

echo "Appending manifest to summary..."
echo '{"timestamp": '$TIMESTAMP', "build": "'$IMAGE_TAG'"}' >> ./summary.jsonl

echo "Copying summary to S3..."
aws $AWS_CLI_ARGS s3 cp ./summary.jsonl $S3_SUMMARY
catch_error $? "aws s3 cp ./summary.jsonl $S3_SUMMARY"

echo "Assets pushed to $S3_DESTINATION"
echo "Manifest pushed to $S3_MANIFEST"
echo "Summary pushed to $S3_SUMMARY"
