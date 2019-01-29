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

* `cd` into whatever directory on your local machine you want to build the site in.
* Create the project root directory. `mkdir intranet && cd intranet` .
* Set up a deploy key in your GitHub account so you can pull the repo via SSH. [Deploy keys](https://developer.github.com/v3/guides/managing-deploy-keys/#deploy-keys)
* Inside the root directory run `git clone git@github.com:ministryofjustice/intranet.git` (You will get an error if your deploy key hasn't been configured correctly).
* Get a `.env` file from team member and copy to the root directory. Or create your `.env` file yourself (see `dotenv.example`) and populate variables (these can be get from the team password manager). Dummy keys can be generated at [Roots salts](https://roots.io/salts.html)
* Get DB from team, unpack (if zipped) and use raw SQL file (named `[something].sql`) and put in the `db-dump` directory.
* Run `sudo nano /private/etc/hosts`. Add `intrant.docker` to your host file (`127.0.0.1	intranet.docker`) and save.
* Make sure you are on internal wifi network or using VPN. This will affect your docker build otherwise.

### Start and stop the web app
* `cd` into `/intranet/docker` folder and make sure `Makefile` is present.
* Run `Make launch` . This executes the Docker commands that build the site image and then spin-up the required containers. It is at this point the build uses Composer to pull in the various repositories and plugins the site uses. This build process takes several minutes.
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

Update as required, either `moj.json` or `bedrock.json` JSON files and commit.
