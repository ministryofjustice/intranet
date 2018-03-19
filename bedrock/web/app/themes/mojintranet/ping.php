<?php

header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');
header('Content-Type: application/json');

$json_string = file_get_contents('build.json');
$data = (array) json_decode($json_string);
$data['now'] = date("Y-m-d H:i:s");

echo json_encode($data);
