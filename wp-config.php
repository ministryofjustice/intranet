<?php
# Database Configuration
define( 'DB_NAME', 'wp_mojintranet' );
define( 'DB_USER', 'mojintranet' );
define( 'DB_PASSWORD', '83fCcuCUamXE7zQRHOrx' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         '@)i$)%(!1/d%yB@hQm |&V@x!sd7UA8?-TWpwiIac;Ox&Irq0Dn9gW>?]7%mUs%i');
define('SECURE_AUTH_KEY',  ',|gk|%n$E9_f+?-EcqXZaJvqzR=eYQ-)6BmMI}C-IFD$-<J]^)&ttE_4R}.@`r^{');
define('LOGGED_IN_KEY',    'Fom(F)ymgBODW#6Y?,<Zru6BG|wq;om9k*G*}v!SklPV[Gs*]h&=Ov]IVLgizxFk');
define('NONCE_KEY',        '6:L=VrH>2&1h01U5-owAr-cA!mV)H@b_w]yv0iI&k2a~=yE!UHC~g*qK!W%vRl;N');
define('AUTH_SALT',        'fL(-KolY#Y-szyj_tEY`s&m.U.kYXAN/fi8HsYnuw]IvQLm8hd;=Pl7Vqa-m$7iY');
define('SECURE_AUTH_SALT', ' |B.HGz*]:L/9yW$svz _A(XX|LV7@D[k)LyKX03`2w,eF4(YYG9qGBhB;=~]cQl');
define('LOGGED_IN_SALT',   '0sQ-kT,l&`REmwUqQA#,n7Au_>d-Qs+Zyj/,u_wc17=_l,`8ytW6s*TEOo~M+jOs');
define('NONCE_SALT',       ';9Sw-0^l4IMC5:EO!];>&+Vg+q*xQ^eAR(wwHfZKO&tFr}d#t(wQk+[ k-*X2>i-');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'mojintranet' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', 'a6213800d88fb6e4241ab85562765a38129081db' );

define( 'WPE_FOOTER_HTML', "" );

define( 'WPE_CLUSTER_ID', '2602' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_CACHE_TYPE', 'generational' );

define( 'WPE_LBMASTER_IP', '176.58.110.97' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'mojintranet.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-2602', );

$wpe_special_ips=array ( 0 => '176.58.110.97', );

$wpe_ec_servers=array ( );

$wpe_largefs=array ( );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( 'default' =>  array ( 0 => 'unix:///tmp/memcached.sock', ), );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings

// define('WP_DEBUG',true);




# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}
