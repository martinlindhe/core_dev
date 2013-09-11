<?php
/**
 * Intended to be run from a cron-job, in order to check a coredev website health
 * by looking at the http://coredevapp/c/selftest page
 */

if ($argc != 2)
    die('Syntax: '.$argv[0]." url\n");

$url = $argv[1];

$data = file_get_contents($url);

if ($data != 'STATUS:OK') {
    echo 'ERROR '.$url."\n\n";
    echo $data;
}

?>
