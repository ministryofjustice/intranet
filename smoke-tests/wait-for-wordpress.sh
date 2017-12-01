#!/bin/bash
set -e

WP_CONTAINER=$1

main() {
  wait_for_wordpress_container
  run_smoke_tests
}


# The docker-compose `depends_on` key reports as soon as the container starts responding. The smoke tests require the
# app to be fully started and will fail if they are triggered as soon as `depends_on` responds. Because the wp container
# installed everything fresh for every test, the setup can take a long time.
wait_for_wordpress_container() {
  while ! curl -I -s ${WP_CONTAINER} | grep -q 'HTTP/1.1'; do
    >&2 echo "Wordpress is unavailable - sleeping..."
    sleep 10
  done

  >&2 echo "Wordpress is up - executing command..."
}

run_smoke_tests() {
  if ! bundle exec cucumber --profile quick_run; then
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
