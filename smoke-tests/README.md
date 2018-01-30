# Intranet Smoke Tests

## Installing Locally

These are standard cucumber features written in Ruby. They will run on
any version of ruby back to `2.2.2`.  Optimally, however, they should be
configured to run under `2.5.0` or higher.

To install current Rubies and set up tests to run locally on OSX:

```bash
# Install homebrew if you don't have it already:
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"

# Next, install rbenv:
brew update
brew install rbenv
rbenv init

# Now, install ruby-build and Ruby:
brew install ruby-build
rbenv install 2.5.0

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

If you do not want to install Ruby, the dockerized version will handle
everyting for you.  If you want to build this way, you can skip the
following section.

## Running Locally

```
cd intranet/smoke-tests
bundle exec cucumber ```

Alternately, you can run these using either `docker` or
`docker-compose`.

See 'Running with Docker on MoJ Jenkins' for instructions on how to run
with the basic docker commands.

## Run _just_ the tests in docker

Edit `smoke-tests/docker-compose.yml` to point at your desired TARGET_URL, then run
`cd smoke-tests; docker-compose up --build`.

## Run the complete stack in docker

This is the simplest way to run the smoketests and is the method that is
being used to run them on travis.

First, ensure you have appropriate AWS keys set in your environment or
your `~/.aws/credentials`. You will need read-only access to
`s3://moj-intranet-smoketest-sql/`, which is where the example SQL dump
is fetched from. If you already have TP AWS credentials set in your
environment, then you should not need to do anything else to make the
fetch work.

Now run the following commands:

```bash
cd docker

make smoketest-nuke-all smoketest
```
Please note that it does take around 10 to 15 minutes for the run
to complete. I have turned off as much of the logging as possible, but
it still generates several thousand lines of output during a typical
run.

You **can** run the tests using `make smoketest`. `smoketest-nuke-all`
is not required.  It is shown in the example to ensure that the test
start in a pristine state, which is always the starting point on travis.
