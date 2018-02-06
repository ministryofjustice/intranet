#!/bin/bash
set -e

WP_CONTAINER=$1
# At the time of writing there are THREE supporting containers required to run the smoketests.
# These are defined in the grep statement, below.
NUMBER_OF_SUPPORTING_CONTAINERS=3

main() {
  wait_for_wordpress_container
  run_smoke_tests
}

fail_if_supporting_containers_fail() {
  # The regex is not in a variable because grep refuses to pick it.
  support_container_count=$(docker ps | grep -cE smoketest_'(wp|db|smtp)')

  if [ "$support_container_count" -ne "$NUMBER_OF_SUPPORTING_CONTAINERS" ]; then
    >&2 echo "***** One of the required supporting containers has failed. Exiting tests."
    stop_all_containers
    exit 1
  fi
}

# The docker-compose `depends_on` key reports as soon as the container starts responding. The smoke tests require the
# app to be fully started and will fail if they are triggered as soon as `depends_on` responds. Because the wp container
# installed everything fresh for every test, the setup can take a long time.
wait_for_wordpress_container() {
  while ! curl -I -s ${WP_CONTAINER} | grep -q 'HTTP/1.1'; do
    >&2 echo "Wordpress is unavailable - sleeping..."
    sleep 10
    # By the time the test container starts, all the other containers should be started. They may not be ready to
    # respond, but they should show up in the output of `docker ps` at least.
    fail_if_supporting_containers_fail
  done

  >&2 echo "Wordpress is up - executing command..."
}

run_smoke_tests() {
  if ! bundle exec cucumber; then
    printf '%s\n' 'Smoke tests failed.\n' >&2
    stop_all_containers
    exit 1
  else
    stop_all_containers
  fi
}

# This requires docker to be installed on the container and `/var/run/docker.sock` to be mounted as a volume in the
# smoketest_tests container:
# volumes:
#   - /var/run/docker.sock:/var/run/docker.sock
stop_all_containers() {
  echo "Stopping WordPress container"
  docker stop smoketest_wp
  echo "Stopping MariaDB container"
  docker stop smoketest_db
  echo "Stopping MailCatcher container"
  docker stop smoketest_smtp
}

main
