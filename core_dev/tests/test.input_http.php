<?php
require_once('/var/www/core_dev/core/input_http.php');
require_once('/var/www/core_dev/core/functions_core.php');

$url = 'http://martincs2.x/gfx/themes/default/logo.png';
if (http_status($url) != 200) echo "FAIL 1\n";

$ts = http_last_modified($url);
echo "File was last modified: ".formatTime($ts)."\n";


echo "\nheaders:\n";
print_r(http_head($url))."\n";

?>
