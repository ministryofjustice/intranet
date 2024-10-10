<div align="center">

# <img alt="MoJ logo" src="https://moj-logos.s3.eu-west-2.amazonaws.com/moj-uk-logo.png" width="200"><br>Intranet

[![Standards Icon]][Standards Link]
[![License Icon]][License Link]

</div>

<br>
<br>

> [!NOTE]  
> This is a project used by the Ministry of Justice UK and agencies.
> https://intranet.justice.gov.uk/

## Features

- Based on [roots/bedrock](https://roots.io/bedrock)
- Dependency management with [Composer](https://getcomposer.org)
- Enhanced password hashing using bcrypt
- Builds into a docker image
- Docker-compose is used to run as a local development server

## Requirements

- PHP >= 8.1
- Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- Docker & docker-compose - [Install](https://www.docker.com/docker-mac)
- Dory (docker proxy for local development) - [Install](https://github.com/FreedomBen/dory)
- Supported version of NodeJS & NPM - [Versions](https://nodejs.org/en/about/releases/), [Install](https://formulae.brew.sh/formula/node)

## Getting Started

1. Clone this repo to your local machine and change directories.
    ```bash
    git clone git@github.com:ministryofjustice/intranet.git .
    cd intranet/
    ```

2. Build the project locally. This will install composer dependencies on your local filesystem.
    ```bash
    make build
    ```

    If you experience any errors at this point, it may be due to being unable to access the private composer repository. [More details here](#private-composer-repository).

3. Start the dory proxy, if it's not already running.
    ```bash
    dory up
    ```

    If you get an error message when trying to start dory, make sure you have docker running.

4. Build and run the docker image.
    ```bash
    make run
    ```
5. Launch the website in your default browser.
    ```bash
    make launch
    ```

6. If this is a new install you may get a development replica by executing the WASM utility.
    ```bash
    wasm migrate intranet2:dev .
    ```

   The WordPress admin area will be accessible at `/wp-admin`.

### Access points

**The application**
> http://intranet.docker/

**Mailcatcher**
> http://mail.intranet.docker/

**phpMyAdmin**
> http://phpmyadmin.intranet.docker:9191/

**Elasticsearch**
> http://elasticsearch.intranet.docker/

**Kibana**
> http://kibana.intranet.docker/


## Composer + WordPress plugins

The installation of WordPress core and plugins is managed by composer.

See `composer.json` for the required packages.

Plugins in the [WordPress plugin repository](https://wordpress.org/plugins/) are available from [WordPress Packagist](https://wpackagist.org/) (wpackagist).

Premium and custom plugins used by MOJ are available in the private composer repository [composer.wp.dsd.io](https://composer.wp.dsd.io).

### WordPress Packagist plugins

Wpackagist plugins are named by their slug on the WordPress plugin repository, prefixed with the vendor `wpackagist-plugin`.

Some examples:

| Plugin name | WordPress plugin URL                         | URL slug      | package name                      |
| ----------- | -------------------------------------------- | ------------- | --------------------------------- |
| Akismet     | https://wordpress.org/plugins/akismet/       | akismet       | `wpackagist-plugin/akismet`       |
| Hello Dolly | https://wordpress.org/plugins/hello-dolly/   | hello-dolly   | `wpackagist-plugin/hello-dolly`   |
| Yoast SEO   | https://wordpress.org/plugins/wordpress-seo/ | wordpress-seo | `wpackagist-plugin/wordpress-seo` |

#### Example: Installing Akismet plugin

Run the following command:

```
composer require "wpackagist-plugin/akismet" "*"
```

This will install the latest version of [Akismet](https://wordpress.org/plugins/akismet/) using the corresponding [wpackagist package](https://wpackagist.org/search?q=akismet).

### Private composer repository

The private composer repository [composer.wp.dsd.io](https://composer.wp.dsd.io) contains premium and custom WordPress plugins.

Access to this repository is restricted. Refer to internal documentation for further details.

### Building theme assets

Theme assets can be built as part of the docker image. Add required commands to `bin/build.sh`.

### Configure the default theme

Set your theme as the default by adding the following line to `config/application.php`:

```php
WP_DEFAULT_THEME = 'clarity';
```

## WP-CLI

The [WordPress CLI](https://wp-cli.org/) is a useful tool for running commands against your WordPress installation.

To use WP-CLI, your docker container must already be running. (This will probably be running in a separate terminal session/tab.)

1. Run:
    ```bash
    make bash
    ```

    A bash session will be opened in the running container.

2. The WP-CLI will be available as `wp`.

    For example, to list all users in the install:
    ```bash
    wp user list
    ```

## Email delivery

When running locally for development, emails sent by WordPress are not delivered. Instead they are captured by [mailcatcher](https://mailcatcher.me/).

To see emails, go to http://mail.`SERVER_NAME` (i.e. the hostname set in your `.env` file) in your browser.
e.g. http://mail.example.docker

This will load a webmail-like interface and display all emails that WordPress has sent.

## Make commands

There are several `make` commands configured in the `Makefile`. These are mostly just convenience wrappers for longer or more complicated commands.

| Command             | Description                                                                                                                                                                                          |
| ------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `make build`        | Run the build script to install application dependencies and build theme assets. This will typically involve installing composer packages and compiling SASS stylesheets.                            |
| `make clean`        | Alias of `git clean -xdf`. Restore the git working copy to its original state. This will remove uncommitted changes and ignored files.                                                               |
| `make docker-clean` | Launches an assistive script to remove docker image caches and persistent data during development. Options: v = Clean volumes. vr = Clean volumes, rebuild and run. n =  Nuke all local Docker images|
| `make run`          | Alias of `docker-compose up`. Launch the application locally using `docker-compose`.                                                                                                                 |
| `make down`         | Alias of `docker-compose down`.                                                                                                                                                                      |
| `make bash`         | Open a bash shell on the WordPress docker container. The [WP-CLI](https://wp-cli.org/) is accessible as `wp`. The application must already be running (e.g. via `make run`) before this can be used. |
| `make launch`       | Checks if the intranet docker instance is running; if not, launch docker in the background and open the site in the systems default browser                                                          |
| `make test`         | Run tests on the application. Out of the box this will run PHP CodeSniffer (code linter).                                                                                                            |
| `make test-fixes`   | Fix issues found during `make test`                                                                                                                                                                  |

## IP whitelisting

The Intranet manages IP whitelisting in a different way to most other deployments. In March 2020 authentication via IdP
was introduced to present access to the MoJ wider network and as a result, IP whitelists moved to AWS ALB listener rules.

To help manage ALB rule creation 2 scripts are available in the JotW LastPass account.

## Bedrock

This project is based on Bedrock. Therefore, much of the Bedrock documentation will be applicable. Bedrock documentation is available at [https://roots.io/bedrock/docs/](https://roots.io/bedrock/docs/).


<!-- License -->

[License Link]: https://github.com/ministryofjustice/intranet/blob/main/LICENSE 'License.'
[License Icon]: https://img.shields.io/github/license/ministryofjustice/intranet?style=for-the-badge

<!-- MoJ Standards -->

[Standards Link]: https://operations-engineering-reports.cloud-platform.service.justice.gov.uk/public-report/intranet 'Repo standards badge.'
[Standards Icon]: https://img.shields.io/endpoint?labelColor=231f20&color=005ea5&style=for-the-badge&url=https%3A%2F%2Foperations-engineering-reports.cloud-platform.service.justice.gov.uk%2Fapi%2Fv1%2Fcompliant_public_repositories%2Fendpoint%2Fintranet&logo=data:image/png;base64,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
