# mojintranet

MoJ Intranet

## Getting started

### Prerequisites

* A recent version of Wordpress
* A working Apache setup with mod_php5

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

After you've installed Wordpress you'll need overwrite the default wp-content directory with
the one found in this repo.

Finally you'll need to make sure that both WP_HOME and WP_SITEURL is defined within wp_config.php like so:

```
define('WP_HOME', 'http://yourdomainforwordpress/');
define('WP_SITEURL', 'http://yourdomainforwordpress/');
```

Finally restart Apache and navigate to the defined domain that you configured in both the vhost config and wp_config.php. Make sure they're the same otherwise you'll get strange errors.
