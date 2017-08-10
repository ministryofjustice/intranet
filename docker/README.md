# Running a local instance of MoJ Intranet

## Pre-requisites

* [Docker](https://www.docker.com/)
* An account on [Docker Hub](https://hub.docker.com/)
* A fast internet connection (for the initial download of docker images etc.)
* Port 80 of your local machine must be available (i.e. no other webserver running)
* MoJ network access (via a VPN is fine). See [Access Requirements](#access-requirements)

## Steps

1. Create your `.env` file (see `dotenv.example`)

  For all the dummy keys (those with the value `saltykey`), you can
visit the following URL to get fresh values to paste into the file;

  https://roots.io/salts.html

2. Prepare your data to load into the Wordpress database

  * Get a recent dump of the production intranet database from the intranet team
  * Unpack the database dump into a raw SQL file (named `[something].sql`) in the `db-dump` directory

3. Edit your `/etc/hosts` file and add the following entries;

  `127.0.0.1	intranet.docker`

  This works for environments where docker is running natively (e.g. on
a Mac). If you have a VM as your docker host, use its IP number, in
place of `127.0.0.1`

4. Login to docker hub

This project uses some public docker images, which require a login
to the docker hub.

  `docker login`

Then enter your docker ID and password.

You can check whether or not you are logged in by running this;

  `docker pull schickling/mailcatcher`

If that works, you will get a few lines of output ending in;

  `Status: Image is up to date for schickling/mailcatcher:latest`

5. Run `make launch`

See the `Makefile` for other useful commands.

6. Load the initial data into the database container

  NB: If you have done this step before, even if you rebuilt your
database container since then, you should not have to do this step
again. The data is written to its own docker volume, so it should still
be there when the mysql container mounts it.  From a separate terminal
window, run this command;

  `make load-db-dump`

7. You should now be able to access your instance of the intranet at
   this URL;

  http://intranet.docker

8. You can now edit files in the `./bedrock_volume` and see changes in
   `intranet.docker`

  NB: The files in this directory are automatically installed by `grunt`
and `composer`, so it is *NOT* a `git` controlled directory. In fact,
the bedrock and config files are explicitly listed in the project
`.gitignore` to prevent accidental checkins. This is necessary until the
structure of the directories is rationalised, which should happen around
the time of the AWS move (written on 2017-08-03).

## <a name="access-requirements"></a> Access Requirements

Because our WP installations use commercial plugins, the
containers must have access to our private repo
(https://composer.wp.dsd.io). This means that they must be run from the
using the VPN, or using an `auth.json` configuration, which provides
httpbasic username/password access to the repo (most commonly used for
CI).

## Mailcatcher

The local versions of the container use
[mailcatcher](https://mailcatcher.me), in its own
container, to handled mail server interaction.  You can access it on
`http://127.0.0.1:1080`.  You can also add the following line to your
`/etc/hosts` to make it accessible on
`http://mail.intranet.docker:1080`, which is probably more useful for
scripitng tests:

```
127.0.0.1 mail.intranet.docker
```

## Production container

The WP production container can be built by copying `dotenv.example` to
`.env.production` and editing its values and by editing
`docker-compose.yml` and adding suitable values where noted.
