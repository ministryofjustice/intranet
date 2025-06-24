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

The widget can be used to see pod registration and deregistration in action, for example on the dev namespace on Cloud Platform:

```bash
# Temporarily scale the deployment up to 5 pods
kubectl -n intranet-dev scale deployment/intranet-dev --replicas=6

# Check the dashboard widget to see the new pods have registered

# After a few seconds, scale the deployment back down to 1 pod (or the value set in deploy/development/deployment.tpl.yml)
kubectl -n intranet-dev scale deployment/intranet-dev --replicas=1

# Check the dashboard widget to see the pods have deregistered
```

## Cache clearing - an example use case

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

### Cache clearing manual test

The cache clearing functionality can be verified, or debugged, on the dev namespace with the following steps:

1.  Temporarily scale the deployment up to 5 pods
    ```bash
    kubectl -n intranet-dev scale deployment/intranet-dev --replicas=5
    ```
2. Login to the WordPress admin dashboard.
3. Grab the JWT cookie value from the browser's developer tools.
4. Open Postman or similar tool:
   - Set the `dw_agency` cookie to `hq`.
   - Use the JWT cookie to access the content, access to the admin area is not necessary.
   - Navigate to a page that is cached, such as `/about-us`.
   - Reload the page to ensure that the content is cached by nginx, check the response headers for `X-Fastcgi-Cache: HIT`.
5. Now, in the WordPress admin dashboard, make an edit to the page, such as changing the title or content.
6. After saving the changes, the nginx cache should be cleared across all pods.
7. Reload the page in Postman to verify that the content has been updated, and check the response headers for `X-Fastcgi-Cache: MISS` to confirm that the cache has been cleared.
8. Finally, scale the deployment back down to 1 pod (or the value set in deploy/development/deployment.tpl.yml):
    ```bash
    kubectl -n intranet-dev scale deployment/intranet-dev --replicas=1
    ```

The staging environment can be tested for QA, with 2 browsers.

1. Have one browser logged in to WordPress,
2. Use an incognito browser to access staging, log in with Entra, but don't log into WordPress.
3. Visit a page in the incognito browser, such as `/about-us`, refresh a few times to ensure that the page is cached by nginx. Check the response headers for `X-Fastcgi-Cache: HIT`.
4. In the logged-in browser, navigate to the WordPress admin dashboard.
3. Make a change to a page in the logged-in browser, such as changing the title or content.
4. Reload the page in the incognito browser to verify that the content has been updated, and check the response headers for `X-Fastcgi-Cache: MISS` to confirm that the cache has been cleared.
