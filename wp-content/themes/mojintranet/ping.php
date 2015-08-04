<?php

header('Content-Type: application/json');

$json_string = file_get_contents('build.json');
$data = (array) json_decode($json_string);
$data['now'] = date("Y-m-d H:i:s");

echo json_encode($data);
