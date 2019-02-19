# Running a local instance
Instructions to run the website on your local machine.

## Requirements

* [Docker](https://www.docker.com/)
* Local version of Node.js (v11.x), NPM (v6.x) and Git
* Port 80 of your local machine must be available.
* MoJ network access (via wifi or VPN). Required when building image as it pulls in protected repos.
* Github account and be added to [Ministry of Justice GitHub account](https://github.com/ministryofjustice)
* AWS Ministry of Justice account (needed for deployment only)
* Access to https://rattic.service.dsd.io
* Your .env file and database copy (provided by team)

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
6. You should now be able to see the intranet running on your local machine, at `http://intranet.docker`.
7. If the site has loaded but there are issues with the CSS and JS see below Compiling assets.

### Compiling assets

For development purposes, we compile assets (CSS and JS) on our local machine. These compiled files however are not used in production. In fact, Git ignores them. Source files are pushed to Git and these are then compiled as part of the Docker build process in the AWS pipline.

### Compiling/running Gulp locally
* `cd` into the Clarity theme `~/intranet/wp-content/themes/clarity`. If you've not compiled the files before, run `Gulp build` and `Gulp resync` to compile and move the files to the correct locations. From then on you can use `Gulp watch` which watches and moves the files as you work.

* `Gulp` which is the default command and starts watching for file changes, compiling and moving files. This should be the one you use in most instances.
* `Gulp build` compiles the JS and CSS but DOESN'T move any files so you won't see changes in your browser.
* `Gulp resync` Doesn't compile only moves the files to the correct location in the Docker folder structure.

### Two themes, one site

Our WP site uses two themes, the parent `mojintranet` and the child theme `clarity`. `mojintranet` is  being deprecated, and we only make use of its functions, none of its frontend assets, CS or JS. Therefore, gulp will only watch for PHP changes in `mojintranet` but will do no asset compiling. 

All asset compiling is done in the `clarity` theme. For more information on how WP parent/child theme work, visit - https://developer.wordpress.org/themes/advanced-topics/child-themes/

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
We use a [Mailtrap](https://mailtrap.io/ ). Details can be found at:
https://rattic.service.dsd.io/cred/detail/1094/

### Prod SMTP host
We use [SendGrid](https://www.sendgrid.com/) . Details can be found at:
https://rattic.service.dsd.io/cred/detail/1285/


## Committing changes to this repository and deployment

1. Branch off master, make code changes to this repository and push to Github. If it is ready for production, create a pull request for another developer in the team to check.
2. Once approved, merge your changes into the master branch. Merge into master branch triggers the code to be pulled into the AWS pipeline, which will require manual approve at both the dev and production stages. For AWS access see service service desk.

## Testing

Team Browserstack.com account information can be found at
https://rattic.service.dsd.io/cred/detail/735/

## Logging

All application logs and performance metrics can be viewed at:
https://eu-west-2.console.aws.amazon.com/cloudwatch/home?region=eu-west-2#dashboards:name=Intranet

### Permissions/permission groups

We have several roles and capabilities setup for testing purposes that refect the actual roles on the site. Login details for each role can be found below:

* Administrator - https://rattic.service.dsd.io/cred/detail/736/
* Agency Admin - https://rattic.service.dsd.io/cred/detail/737/
* Agency Editor - https://rattic.service.dsd.io/cred/detail/1289/
* Regional Editor - https://rattic.service.dsd.io/cred/detail/1290/
* Subscriber - https://rattic.service.dsd.io/cred/detail/1294/
* Team Author - https://rattic.service.dsd.io/cred/detail/1292/
* Team Lead - N/A (TODO re-add this one)


## Updating Wordpress, plugins and supporting repository versions

Update as required, either `moj.json` or `bedrock.json` JSON files and commit. Plugins can be added by adding a new row with the plugin details. All plugins are pulled from a public repository called https://wpackagist.org/. Make sure before you change the plugin version it is in this public repo.

### Private plugin repositories

We are using paid for, commercial plugins that cannot be in the public repo. To allow us privileged access to the private repository, see https://composer.wp.dsd.io (you need to be on the MoJ network to access). And this is hosted but us in the department. To add new versions into this repo you need to update the [satis.json file](https://github.com/ministryofjustice/pp-satis-config/blob/master/satis.json).
