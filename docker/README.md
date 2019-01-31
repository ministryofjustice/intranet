# Running a local instance
Instructions to run the website on your local machine.

## Requirements

* [Docker](https://www.docker.com/)
* Local version of Node.js, NPM and Git
* Port 80 of your local machine must be available.
* MoJ network access (via wifi or VPN). Required when building image as it pulls in protected repos.
* Github account and be added to [Ministry of Justice GitHub account](https://github.com/ministryofjustice)
* AWS Ministry of Justice account (needed for deployment only)
* Your .env file provided by team and database copy

## Getting Started

### Download the repo and setup the environment

* Clone repo into your chosen directory `git clone git@github.com:ministryofjustice/intranet.git` (You may need to setup a deploy key in your github account, if you get an error).
* Set up new local host file address (Run `sudo nano /private/etc/hosts`. Add `intrant.docker` to your host file (`127.0.0.1	intranet.docker`) and save).

### Build and run the website

1. Make sure you are on an MoJ network or VPN whitelisted connection.
2. `cd` into the `docker` folder.
4. Request an `.env` file from team member and copy into `docker` folder. Dummy keys, if needed can be generated at [Roots salts](https://roots.io/salts.html)
3. `Make launch`. Running this command in the docker folder both builds and spins up docker containers. Composer also pulls in the various repositories and plugins the site uses. This build process takes several minutes when you first run it. It executes out of daemon mode, so when the site is running you will see the stream of log files in your terminal.
4. Open a new terminal window and run `docker ps`. Check that everything is running: `docker_wordpress`, `mariadb` amd `mailcatcher`.
5. Get database copy from team, unpack (if zipped) and use raw SQL file (named `[something].sql`) and put in the `db-dump` directory in the `docker` folder.
5. `cd` into `docker` folder and run `Make load-db-dump`. This populates the database for WP.
6. You should now be able to see the intranet running on your local machine, at `http://intranet.docker` but CSS and JS will not be complied.
7. Compile CSS and JS. `cd` into `~/intranet/wp-content/themes/clarity`. Run `npm install && gulp`. Then copy compiled files to `~/intranet/docker/bedrock_volume/web/app/themes/intranet-theme-clarity`. You may want to create your own automated process for copying these files. TODO// fix this legacy copying issue.
8. Visit `http://intranet.docker` you should have a fully working intranet on your local machine.

### Troubleshooting

For further frontend issues or information visit this repo's [wiki](https://github.com/ministryofjustice/intranet/wiki).

### Shutdown website
To spin the docker containers down, `cd` into `docker` folder and run `Make shutdown` cmd.
* See `Makefile` in the docker directory for other useful commands.

## Email delivery

### Local SMTP host
When running locally for development, emails sent by WordPress are not delivered. Instead they are captured by [mailcatcher](https://mailcatcher.me/).
To see emails, go to http://intranet.docker:1080/ or http://127.0.0.1:1080 . This will load a webmail-like interface and display all emails that WordPress has sent.

### Dev SMTP host
We use a [Mailtrap](https://mailtrap.io/ ). Account details are found in team password manger.

### Prod SMTP host
We use [SendGrid](https://www.sendgrid.com/) . Details are in team password manager.

## Committing changes to this repository and deployment

1. Branch off master, make code changes to this repository and push to Github. If it is ready for production, create a pull request for another developer in the team to check.
2. Once approved, merge your changes into the master branch. Merge into master branch triggers the code to be pulled into the AWS pipeline, which will require manual approve at both the dev and production stages. For AWS access see service service desk.

## Updating Wordpress, plugins and supporting repository versions

Update as required, either `moj.json` or `bedrock.json` JSON files and commit. Plugins can be added by adding a new row with the plugin details. All plugins are pulled from a public repository called https://wpackagist.org/. Make sure before you change the plugin version it is in this public repo.

### Private plugin repositories

We are using a few paid for, commercial plugins that obviously cannot be in the public repo mentioned above. To solve this we have our own private repo https://composer.wp.dsd.io (you need to be on the MoJ network to access). And this is hosted but us in the department. To add new versions into this repo you need to update the [satis.json file](https://github.com/ministryofjustice/pp-satis-config/blob/master/satis.json).
