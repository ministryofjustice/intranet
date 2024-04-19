<div align="center">

# <img alt="MoJ logo" src="https://moj-logos.s3.eu-west-2.amazonaws.com/moj-uk-logo.png" width="200"><br>Intranet

[![Standards Icon]][Standards Link]
[![License Icon]][License Link]

</div>

<br>
<br>


This is a project used by Ministry of Justice UK and agencies.
https://intranet.justice.gov.uk/

</div>

## Summary

> Nb. `README.md` is located in `.github/`, the preferred location for a clean repository.


## Installation for development

The application uses Docker. This repository provides two separate local test environments, namely:

1. Docker Compose
2. Kubernetes

Where `docker compose` provides a pre-production environment to develop features and apply upgrades, Kubernetes allows
us to test and debug our deployments to the Cloud Platform.

### Setup

In a terminal, move to the directory where you want to install the application. You may then run:

```bash
git clone https://github.com/ministryofjustice/intranet.git
```

Change directories:

```bash
cd intranet
```

Next, depending on the environment you would like to launch, do one of the following.

### 1. Docker Compose

This environment has been set up to develop and improve the application.

#### Requirements

- [Docker](https://www.docker.com/products/docker-desktop/)
- [Dory](https://formulae.brew.sh/formula/dory) (by FreedomBen) _auto-install available_

The following make command will get you up and running.

It creates the environment, starts all services and opens a command prompt on the container that houses our PHP code,
the service is called `php-fpm`:

```bash
make
```

During the `make` process, the Dory proxy will attempt to install. You will be guided though an installation, if needed.

### Services

You will have five services running with different access points. They are:

**Nginx**<br>
http://intranet.docker/

**PHP-FPM**<br>

```bash
make bash
```

On first use, the application will need initializing with the following command.

```bash
composer install
```

**Node**<br>
This service watches and compiles our assets, no need to access. The output of this service is available on STDOUT.

When working with JS files in the `src` directory it can be useful to develop from inside the node container. 
Using a *devcontainer* will allow the editor to have access to the `node_modules` directory, which is good for intellisense and type-safety.
When using a devcontainer, first start the required services with `make` and then open the project in the devcontainer. 
Be sure to keep an eye on the node container's terminal output for any laravel mix errors.

The folder `src/components` is used for when it makes sense to keep a group of scss/js/php files together.
The folder `src/components/post-meta` is an example where php is required to register fields in the backend, and js is used to register fields in the frontend.

**MariaDB**<br>
Internally accessed by PHP-FPM on port 3306

**PHPMyAdmin**<br>
http://intranet.docker:9191/ <br>
Login details located in `docker-compose.yml`

> There is no need to install application software on your computer.<br>
> All required software is built within the services and all services are ephemeral.

#### Volumes

There are multiple volume mounts created in this project and shared across the services.
The approach has been taken to speed up and optimise the development experience.

### 2. Kubernetes

This environment is useful to test Kubernetes deployment scripts.

Local setup attempts to get as close to development on Cloud Platform as possible, with a production-first approach.

#### Requirements

- [Docker](https://www.docker.com/products/docker-desktop/)
- [kubectl](https://kubernetes.io/docs/tasks/tools/)
- [Kind](https://kind.sigs.k8s.io/docs/user/quick-start/#installation)
- Hosts file update, you could...
  > `sudo nano /etc/hosts`<br>... on a new line, add: `127.0.0.1	intranet.local`

Once the above requirements have been met, we are able to launch our application by executing the following make
command:

```bash
make local-kube
```

The following will take place:

1. If running; the Dory proxy is stopped
2. A Kind cluster is created with configuration from: `deploy/config/local/cluster.yml`
3. The cluster Ingress is configured
4. Nginx and PHP-FPM images are built
5. Images are transferred to the Kind Control Plane
6. Local deployment is applied using `kubectl apply -f deploy/local`
7. Verifies pods using `kubectl get pods -w`

Access the running application here:
**http://intranet.local/**

#### Volumes

In the MariaDB YAML file you will notice a persistent volume claim. This will assist you in keeping application data,
preventing you from having to reinstall WordPress every time you stop and start the service.

### Secrets

[Most secrets are managed via GitHub settings](https://github.com/ministryofjustice/intranet/settings/secrets/actions)

#### WP Keys & Salts

It is the intention that WordPress keys and salts are auto generated, before the initial GHA
build stage. Lots of testing occurred yet the result wasn't desired; dynamic secrets could not be hidden in
the log outputs. Due to this, secrets are managed in settings.

### Kubernetes - quick command reference

```bash
# Make interaction a little easier; we can create repeatable
# variables, our namespace is the same name as the app, defined
# in ./deploy/development/deployment.tpl
#
# If interacting with a different stack, change the NSP var.
# For example;
# - production, change to 'intranet-prod'

# Set some vars, gets the first available pod
NSP="intranet-dev"; \
POD=$(kubectl -n $NSP get pod -l app=$NSP -o jsonpath="{.items[0].metadata.name}");

# Local interaction is a little different:
# - local, change NSP to `default` and app to `intranet-local`
NSP="default"; \
POD=$(kubectl -n $NSP get pod -l app=intranet-local -o jsonpath="{.items[0].metadata.name}");
```

After setting the above variables (via `copy -> paste -> execute`) the following blocks of commands will work
using `copy -> paste -> execute` too.

```bash
# list available pods and their status for the namespace
kubectl get pods -n $NSP

# watch for updates, add the -w flag
kubectl get pods -w -n $NSP

# describe the first available pod
kubectl describe pods -n $NSP

# monitor the system log of the first pod container
kubectl logs -f $POD -n $NSP

# monitor the system log of the fpm container
kubectl logs -f $POD -n $NSP fpm

# open an interactive shell on an active pod
kubectl exec -it $POD -n $NSP -- ash

# open an interactive shell on the FPM container
kubectl exec -it $POD -n $NSP -c fpm -- ash
```

## AWS setup

### S3

Create a bucket with the following settings:

- Region: `eu-west-2`
- Object Ownership: 
   - ACLs enabled
   - Bucket owner preferred
- Block all public access:
  - Block public access to buckets and objects granted through new access control lists (ACLs): NO
  - Block public access to buckets and objects granted through any access control lists (ACLs): YES
  - Block public access to buckets and objects granted through new public bucket or access point policies: YES
  - Block public and cross-account access to buckets and objects through any public bucket or access point policies: YES

### CloudFront

Create a deployment with the following settings:

- Cache key and origin requests
    - Legacy cache settings
       - Query strings: All

To restrict access to the Amazon S3 bucket follow the guide to implement origin access control (OAC) 
https://repost.aws/knowledge-center/cloudfront-access-to-amazon-s3

### IAM

For using u user's keys, create a user with a policy similar to the following:

```json
{
  "Sid": "s3-bucket-access",
  "Effect": "Allow",
  "Action": "s3:*",
  "Resource": "arn:aws:s3:::bucket-name"
}
```

An access key can then be used for testing actions related to the S3 bucket, use env vars:

- AWS_ACCESS_KEY_ID
- AWS_SECRET_ACCESS_KEY

When deployed, server-roles should be used.

### Verify WP Offload Media

To verify that S3 & CloudFront are working correctly.

- Go to WP Offload Media Lite settings page. There should be green checks for Storage & Delivery settings.
- Upload an image via Media Library.
- Image should be shown correctly in the Media Library.
- The img source domain should be CloudFront.
- Directly trying to access an image via the S3 bucket url should return an access denied message.

## Azure Setup

### Useful links

- [Ministry of Justice | Overview](https://portal.azure.com/#view/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/~/Overview)
- App [MOJ-Local-Intranet-v2](https://portal.azure.com/#view/Microsoft_AAD_RegisteredApps/ApplicationMenuBlade/~/Overview/appId/73ed65a5-e879-4027-beab-f5e64de803b7/isMSAApp~/false)
- App [MOJ-Dev-Intranet-V2](https://portal.azure.com/#view/Microsoft_AAD_RegisteredApps/ApplicationMenuBlade/~/Overview/quickStartType~/null/sourceType/Microsoft_AAD_IAM/appId/1dac3cbf-91d2-4c0e-9c80-0bf3f8fabd75)

### Register an application

1. Go to the Azure portal and sign in with your account.
2. Click on the `Microsoft Entra ID` service.
3. Click on `App registrations`.
4. Click on `New registration`.
5. Fill in the form (adjust to the environment):
   - Name: `MOJ-Local-Intranet-v2`
   - Supported account types: `Accounts in this organizational directory only`
   - Redirect URI: `Web` and `http://localhost/oauth2?action=callback`  
     or `https://dev.intranet.justice.gov.uk/oauth2?action=callback` etc.
6. Copy the `Application (client) ID` and `Directory (tenant) ID` values,
  make them available as environment variables `OAUTH_CLIENT_ID`, `OAUTH_TENNANT_ID`.
7. Click on `Certificates & secrets` > `New client secret`.
8. Fill in the form:
   - Description: `Local-Intranet-v2`
   - Expires: `6 months`
9. Set a reminder to update the client secret before it expires.
10. Copy the `Value` value, make it available as environment variable `OAUTH_CLIENT_SECRET`.
11. Click on `Expose an API` > `Add a scope`.
12. Use the default Application ID URI, which is `api://<client_id>`.
13. Fill in the form:
    - Scope name: `user_impersonation`
    - Who can consent: `Admins and users`
    - Admin consent display name: `Access Intranet`
    - Admin consent description: `Access Intranet on behalf of the signed-in user`
    - User consent display name: `Access Intranet`
    - User consent description: `Access Intranet on your behalf`
14. Click on `Add a client application`.
15. Enter the Client ID of the application you created.
16. Check the box next to the application you created.
17. Click on `Add application`.

The oauth2 flow should now work with the Azure AD/Entra ID application.
You can get an Access Token, Refresh Token and an expiry of the token.


<!-- License -->

[License Link]: https://github.com/ministryofjustice/intranet/blob/main/LICENSE 'License.'
[License Icon]: https://img.shields.io/github/license/ministryofjustice/intranet?style=for-the-badge

<!-- MoJ Standards -->

[Standards Link]: https://operations-engineering-reports.cloud-platform.service.justice.gov.uk/public-report/intranet 'Repo standards badge.'
[Standards Icon]: https://img.shields.io/endpoint?labelColor=231f20&color=005ea5&style=for-the-badge&url=https%3A%2F%2Foperations-engineering-reports.cloud-platform.service.justice.gov.uk%2Fapi%2Fv1%2Fcompliant_public_repositories%2Fendpoint%2Fintranet&logo=data:image/png;base64,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
