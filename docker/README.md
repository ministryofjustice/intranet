# Running a local instance
Instructions to run the website on your local machine.

## Requirements

* [Docker](https://www.docker.com/)
* Port 80 of your local machine must be available.
* MoJ network access (via wifi or VPN). Required when building image as it pulls in protected repos.
* Github account and be added to [Ministry of Justice GitHub account](https://github.com/ministryofjustice)
* AWS Ministry of Justice account (needed for deployment only)
* Your .env file provided by team and database copy

## Getting Started

Via CMD line in your terminal (Mac OS):

* `cd` into the chosen directory on your local machine you want to build the site in.
* Create the project root directory. `mkdir intranet && cd intranet` .
* Inside the root directory run `git clone git@github.com:ministryofjustice/intranet.git` (You may need to setup a deploy key in your github account, if you get an error).
* Request an `.env` file from team member and copy to the root directory. Or create your `.env` file yourself (see `dotenv.example`) and populate variables (these can be get from the team password manager). Dummy keys can be generated at [Roots salts](https://roots.io/salts.html)
* Get database copy from team, unpack (if zipped) and use raw SQL file (named `[something].sql`) and put in the `db-dump` directory.
* Set up new local hostfile address (Run `sudo nano /private/etc/hosts`. Add `intrant.docker` to your host file (`127.0.0.1	intranet.docker`) and save).

### Build and run the website
* Make sure you are on an MoJ network or VPN (required for docker build stage).
* `cd` into `~/docker` folder where you should see a `Makefile`.
* In the docker folder, with command line run `Make launch` . This executes the Docker commands that build the site image and then spin-up the required containers. It is at this point the build uses Composer to pull in the various repositories and plugins the site uses. This build process takes several minutes.
* Once finished, check Docker containers are running using `docker ps`. You should see three containers running, `docker_wordpress`, `mariadb` amd `mailcatcher`.
* Load database, run `make load-db-dump` at this stage.
* You should now be able to see the intranet running on your local machine, at `http://intranet.docker`.
* You may need to compile the site assets at this stage (CSS and JS). `cd` into child theme `Clarity` at `~/intranet/wp-content/themes/clarity
`. Run `npm install` (if packages are not installed) and then run `gulp` , our compiling tool. If you can't see any changes, follow instructions below `I can't see my changes I've made to the theme?`.
* To spin the containers down, you can use the command `Make shutdown` . See `Makefile` for other useful commands.

## Email delivery

### Local SMTP host
When running locally for development, emails sent by WordPress are not delivered. Instead they are captured by [mailcatcher](https://mailcatcher.me/).
To see emails, go to http://intranet.docker:1080/ or http://127.0.0.1:1080 . This will load a webmail-like interface and display all emails that WordPress has sent.

### Dev SMTP host
We use a [Mailtrap](https://mailtrap.io/ ). Account details are found in team password manger.

### Prod SMTP host
We use [SendGrid](https://www.sendgrid.com/) . Details are in team password manager.

## Committing changes to this repository and deployment

1.) Branch off master, make code changes to this repository and push to Github. If it is ready for production, create a pull request for another developer in the team to check.

2.) Once approved, merge your changes into the master branch. This then moves to the AWS pipeline, which will require manual approve at both the dev and production stages. For AWS access see service service desk.

### I can't see my changes I've made to the theme?
To make code edits to the themes, edit in `~/intranet/wp-content/themes/mojintranet` and `~/intranet/wp-content/themes/clarity`.
Because of legacy architectural decisions, code changes made to either theme (`Clairty` of `mojintranet`) have to be manually move one you've made changes in them to the volume mounted by Docker at `~/intranet/docker/bedrock_volume/web/app/themes/` and `~/intranet/docker/bedrock_volume/web/app/themes/intranet-theme-clarity`

Example `Make` command you could setup to move the files after each code change.

```
move:
  rsync -a --delete ~/intranet/wp-content/themes/mojintranet ~/Dev/moj/intranet/docker/bedrock_volume/web/app/themes/
  rsync -a --delete ~/intranet/wp-content/themes/clarity/* ~/Dev/moj/intranet/docker/bedrock_volume/web/app/themes/intranet-theme-clarity
```

## Updating Wordpress, plugins and supporting repository versions

Update as required, either `moj.json` or `bedrock.json` JSON files and commit. Plugins can be added by adding a new row with the plugin details. All plugins are pulled from a public repository called https://wpackagist.org/. Make sure before you change the plugin version it is in this public repo.

### Private plugin repositories

We are using a few paid for, commercial plugins that obviously cannot be in the public repo mentioned above. To solve this we have our own private repo https://composer.wp.dsd.io (you need to be on the MoJ network to access). And this is hosted but us in the department. To add new versions into this repo you need to update the [satis.json file](https://github.com/ministryofjustice/pp-satis-config/blob/master/satis.json).
