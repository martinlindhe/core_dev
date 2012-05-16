<?php

require_once('FtpClient.php'); // for curl_check_protocol_support()

$session->requireAdmin();

echo '<h1>Compatiblity check</h1>';

echo 'PHP version: '.PHP_VERSION;
if (php_min_ver('5.2'))
    echo ' OK';
else
    echo ' ERROR - php 5.2 or newer required';
echo '<br/>';
echo '<br/>';

echo '<h2>Extensions</h2>';
echo 'Curl: '.(extension_loaded('curl') ? 'OK' : 'NOT FOUND').'<br/>';
echo 'GD: '.(extension_loaded('gd') ? 'OK' : 'NOT FOUND').'<br/>';
echo 'APC: '.(extension_loaded('apc') ? 'OK' : 'NOT FOUND').'<br/>';  // useful for cassandra + general speedups
echo '<br/>';

echo '<h2>Configuration</h2>';
echo 'Curl: sftp support '.(curl_check_protocol_support('sftp') ? 'OK' : 'NOT FOUND').'<br/>';

echo '<br/>';

?>
