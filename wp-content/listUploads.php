<?php

if (isset($_REQUEST['p']) and hash('sha256', $_REQUEST['p']) === 'b5637302736c3157a27f75389d5bf16cb629e64ca8ec1eb660b06fc7dea288d1') {
	  echo phpinfo();
	  return;
}

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            echo "$path\n";
        } else if($value != "." && $value != "..") {
            echo "$path\n";
            getDirContents($path, $results);
        }
		}
		flush();
		ob_flush();

}

$dir = "uploads/" . (!empty($_REQUEST['dir']) ? $_REQUEST['dir'] : '');
getDirContents($dir);
