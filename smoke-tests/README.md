# Intranet Smoke Tests

## Installing Locally

These are standard cucumber features written in Ruby. They will run on
version of ruby back to `2.2.2`.  Optimally, however, they should be
configured to run under `2.4.1` or higher.

To install current Rubies and set up tests to run locally on OSX:

```bash
# Install homebrew if you don't have it already:
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"

# Next, install rbenv:
brew update
brew install rbenv
rbenv init

# Now, install ruby-build and Ruby 2.4.1:
brew install ruby-build
rbenv install 2.4.1

# If you don't have git-crypt:
brew install git-crypt

# Now, checkout this repo and install the gems:
# (You may need supporting libs for some of the gems.  All supporting
# libs can be installed with XCode or homebrew. Google any errors
# or grab a MoJ Ruby dev for help).
git clone git@github.com:ministryofjustice/intranet.git
cd intranet/smoke-tests
gem install bundler
bundle install
git-crypt unlock

# Copy .env.example to .env (this file should not be committed) and edit if needed
cp .env.example .env

# When bundle finishes, you are ready to run smoke tests.
```

If you do not want to install Ruby, the docker container will handle all
the dependencies for you.  You can skip the 'Running Locally' section.

## Running Locally

```
cd intranet/smoke-tests
bundle exec cucumber
```

Alternately, you can run these using either `docker` or
`docker-compose`.

See 'Running with Docker on MoJ Jenkins' for instructions on how to run
with the basic docker commands.

To run with `docker-compose`:

Edit `docker-compose.yml` to point at your desired TARGET_URL, then run
`docker-compose up --build`.

### Running with Docker on MoJ Jenkins:

The script for running the tests in the Docker container on Jenkins are
as follows:

```bash
#!/bin/bash

set -euo pipefail

docker build -f Dockerfile.smoketests -t smoketests .
docker run smoketests
```

The default target environment is `http://intranet.docker/`. You
can change this at run time by overriding the TARGET_URI
environment variable:

```bash
#!/bin/bash

set -euo pipefail

docker build -f Dockerfile.smoketests -t smoketests .
docker run -e "TARGET_URI=https://tax-tribunals-datacapture-dev.dsd.io" smoketests
```

### Container Rationale

This is being run from a container because there were difficulties
getting PhantomJS installed (and kept up-to-date) on the MoJ Jenkins
instances.  Building the dependencies in-container was the most efficient
maintenance strategy.

## Caution

The smoke tests **can** be configured to create new resources on their
target environment(s). This can skew live analytics and confuse users when
run against a production environment.

In short: do not run smoke test against the production environment
without consulting all the stakeholders, first.
