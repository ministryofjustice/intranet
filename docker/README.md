# Running a local instance of MoJ Intranet

1. Create your `.env` file (see `dotenv.example`)

For all the dummy keys (those with the value `saltykey`), you can visit the following URL to get fresh values to paste into the file;

    https://roots.io/salts.html

2. Prepare your data to load into the Wordpress database

  * Get a recent dump of the production intranet database from the intranet team
  * Unpack the database dump into a raw SQL file (named `[something].sql`) in the `db-dump` directory

3. Edit your `/etc/hosts` file and add the following entries;

    127.0.0.1	intranet.docker

This works for environments where docker is running natively (e.g. on a Mac). If you have a VM
as your docker host, use its IP number, in place of `127.0.0.1`

4. Run `docker-compose up`

5. Load the initial data into the database container

NB: If you have done this step before, even if you rebuilt your database container since then, you should not have to do this step again. The data is written to its own docker volume, so it should still be there when the mysql container mounts it.

From a separate terminal window, run this command;

    docker-compose exec mysql bash -c 'cat /db-dump/*.sql | mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE}'

6. You should now be able to access your instance of the intranet at this URL;

    http://intranet.docker

