# Cluster Helper Plugin for WordPress

The cluster helper plugin is designed to assist with managing WordPress in a clustered environment. 

Specifically, it has been developed to handle clearing of nginx cache across all pods in a cluster.
But, the functions are versatile and can be used for other purposes as well.

## Associated files

- `cluster-helper.php`: The main plugin file that contains the core functionality.
- `commands.php`: This file contains the WP_CLI commands exposed by the plugin.
- `README.md`: This file, which provides an overview of the plugin and its usage.
- `./deploy/config/init/fpm-start.sh`: A script that is executed when the PHP-FPM service starts, which includes the command `wp cluster-helper register-self`.
- `./bin/fpm-stop.sh`: A script that is executed when the PHP-FPM service stops, which includes the command `wp cluster-helper unregister-self`.

## Associated configuration

- `docker-compose.yml`: Here we use a `pre_stop` hook to run the `fpm-stop.sh` script, which unregisters the current pod from the cluster helper.
- `deploy/*/deployment.tpl.yml`: Here we use a `preStop` hook to run the `fpm-stop.sh` script, which unregisters the current pod from the cluster helper.
   The environment variable `NGINX_IP` is made available to the fpm container, which is used to identify the pod to other pods in the cluster.
- `config/application.php`: Here, the config variable `NGINX_HOST` is derived from `NGINX_IP`.

## How it works

An option in the WordPress database is used to hold the list of pods in the cluster. 
When a pod starts, it registers itself by adding its endpoint (e.g. `http://<NGINX_IP>:8080`) to this list. 
When a pod stops, it deregisters itself by removing its IP from the list.

Periodically, the plugin will check if the list of pods is up to date.
If a pod is no longer reachable, it will be removed from the list.

## Dashboard Widget

A dashboard widget is provided to display the current list of pods in the cluster.
This widget can be used to monitor the status of the cluster and ensure that all pods are registered and deregistered correctly.

## Example usage

In the file `public/app/themes/clarity/inc/admin/page.php` you can find an example of how to use the cluster helper plugin to clear the nginx cache across all pods in the cluster.

1. In the function `clear_nginx_cache`, a list of all nginx hosts is retrieved by running:

```php
$cluster_helper = new ClusterHelper();
$nginx_hosts = $cluster_helper->getNginxHosts('hosts');
```

2. For the post id in question, the path is constructed. This is essential, as it is part of nginx's cache key, and is part of the purge cache url.

```php
$post_url = get_permalink($post_id);
$post_path = parse_url($post_url, PHP_URL_PATH);
```

3. The hosts are then iterated over, and for each host, the command to clear the nginx cache is run:

```php
foreach ($nginx_hosts as $host) {
    // Construct the full URL for the purge request.
    $nginx_cache_path = $host . '/purge-cache' . $post_path;

    // Purge the cache.
    wp_remote_get($nginx_cache_path);

    // ...
}
```
