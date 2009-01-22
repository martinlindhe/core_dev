<?php
require_once('/var/www/core_dev/core/core.php');
require_once('/var/www/core_dev/core/input_http.php');

$url = 'http://martincs2.x/gfx/themes/default/logo.png';
if (http_status($url) != 200) echo "FAIL 1\n";

$headers = http_head($url);
echo "\nheaders:\n";
print_r($headers)."\n";

echo "last modified: ".formatTime(http_last_modified($headers))."\n";

echo "content-length: ".http_content_length($headers)."\n";

?>
