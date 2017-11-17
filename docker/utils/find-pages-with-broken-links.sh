#!/bin/bash

# A quick and dirty script to find posts/pages with links to the old `wp-moj-prod` S3 bucket.
# To run:
# Start the local dev docker instance;
# Copy this script into `intranet/docker/bedrock_volume`;
# shell into the local wp docker instance:
# ```
#  docker exec -it wp_local_dev bash`
#  cd /bedrock
#  . ./find-pages-with-broken-links.sh
# ```
# The results will be saved to `intranet/docker/bedrock_volume/bad_link_report.json

set -e

USERS_BY_ID=$(
 wp --path=/bedrock/web/wp --allow-root user list --format=json | jq '[.[] | [(.ID|tostring), .user_email]] | reduce .[] as $i ({}; .[$i[0]] = $i[1])'
)

echo "** Loading posts"
if [ ! -f _all_posts.json ]; then
  wp --path=/bedrock/web/wp --allow-root --format=json \
    --fields=ID,post_author,post_name,post_content,url,post_type,post_status \
    --post_type=page,post,event,news,regional_page,regional_news,document,revision,acf-field \
    post list 2>/dev/null > _all_posts.json
fi

echo "** Finding posts with bad links"
jq ".[] | select(.post_content | test(\"moj-wp-prod\")) | { ID: .ID, user: $USERS_BY_ID[.post_author], url: .url, status: .post_status }" _all_posts.json \
  > _posts_with_bad_links.json

IDS=$(jq '.[] | .ID' _all_posts.json | sort -n -r)

echo "** Loading metadata for posts"
if [ ! -d _post_metas ]; then
  mkdir _post_metas
fi

for id in $IDS; do
  echo "Processing metadata for post $id"
  if [ ! -f _post_metas/$id.json ]; then
    wp --path=/bedrock/web/wp --allow-root post meta list $id --format=json > _post_metas/$id.json 2> /dev/null
  fi
done

echo

echo "** Finding metadata with bad links"
META_IDS=$(for n in {1..9}; do
  jq '.[] | {post_id: .post_id, meta_value: .meta_value|tostring}' /bedrock/_post_metas/$n*.json |
  jq -s '.' |
  jq '.[] | select(.meta_value | test("moj-wp-prod")) | .post_id'
done | sort -nu)

for id in $META_IDS; do
  jq ".[] | select(.ID==$id) | { ID: .ID, user: $USERS_BY_ID[.post_author], url: .url, status: .post_status }" _all_posts.json >> _posts_with_bad_links.json
done

jq -s '. | unique_by(.url)' _posts_with_bad_links.json > bad_link_report.json
