<?php

set_time_limit(600);

require_once('wurfl_config.php');

$start =  microtime(true); 

$wurflObj = new wurfl_class();

$init_class =  microtime(true);

//Testing client agent
$wurflObj->GetDeviceCapabilitiesFromAgent($_SERVER['HTTP_USER_AGENT']);

$end =  microtime(true);

echo 'Time to initialize class: '.round($init_class-$start,6).'<br/>';
echo 'Time to find the user agent: '.round($end-$init_class,6).'<br/>';
echo 'Total: '.round($end-$start,6).'<br/>';

echo '<pre>';
var_export($wurflObj->capabilities);
echo '</pre>';
?>
