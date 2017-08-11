# MoJ Intranet Theme

## Getting started

You'll need:

* The most recent public release of WordPress
* A working Apache setup with mod_php5
* Enable mod_rewrite within the Apache config
* Mysql 5.6 and above or MariaDB 5.5

If you're on Windows or OS X you might find it easier to use a tool such as WAMP or MAMP. On OS X PHP, Apache come pre-installed. Mysql or MariaDB can be installed easily via brew if you're on a mac.

An example of a working vhost config for Apache would look similiar to this:

```
<VirtualHost *:80>
  LoadModule php5_module /home/ubuntu/.phpenv/versions/5.4.5/libexec/apache2/libphp5.so

  DocumentRoot /path/to/you/web/root/www
  ServerName your_domain.com
  <FilesMatch \.php$>
    SetHandler application/x-httpd-php
  </FilesMatch>
</VirtualHost>
```

Change the lines pointing to where libphp5 is installed on your system and also the domain.

Also you'll need to enable short tags. If you don't have access to you systems php.ini then the easiest way of doing this is by enabling this within a .htaccess within the WordPress root directory.

An example .htaccess example including this and containing the the rules for pretty routes would look like this:

```
<IfModule mod_php5.c>
  php_value short_open_tag 1
</IfModule>

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
```

After you've installed WordPress you'll need to overwrite the default wp-content directory with
the one found in this repo.

Finally you'll need to make sure that both WP_HOME and WP_SITEURL is defined within wp_config.php like so:

```
define('WP_HOME', 'http://yourdomainforwordpress/');
define('WP_SITEURL', 'http://yourdomainforwordpress/');
```

Finally restart Apache and navigate to the defined domain that you configured in both the vhost config and wp_config.php. Make sure they're the same otherwise you'll get strange errors.

### Plugins

There are some required plugins that need to be installed in order to use this theme. Some of them are included as a git submodule and can be installed by typing:

```
git submodule update --init --recursive
```
There are others which need to be downloaded and installed seperately. These include:

* Amazon Web Services https://wordpress.org/plugins/amazon-web-services/
* CMS Tree Page View https://wordpress.org/plugins/cms-tree-page-view/
* Live Drafts https://wordpress.org/plugins/live-drafts/
* Pods (for custom content types and fields) https://wordpress.org/plugins/pods/
* Recently Edited Content Widget https://wordpress.org/plugins/recently-edited-content-widget/
* Relevanssi Premium (for search) https://wordpress.org/plugins/relevanssi/
* WP Document Revisions (version control for documents) https://wordpress.org/plugins/wp-document-revisions/
* WP Offload S3 https://wordpress.org/plugins/amazon-s3-and-cloudfront/

