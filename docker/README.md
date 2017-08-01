# Running a local instance of MoJ Intranet

1. Create your `.env` file (see `dotenv.example`)

2. Prepare your data to load into the Wordpress database

  * Get a recent dump of the production intranet database from the intranet team
  * Unpack the database dump into a raw SQL file (named `[something].sql`) in the `db-dump` directory

2. Edit your `/etc/hosts` file and add the following entries;

3. Run `docker-compose up`

4. Load the initial data into the database container

From a separate terminal window, run this command;

    docker-compose exec mysql bash -c 'cat /db-dump/*.sql | mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE}'
