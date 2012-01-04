<?php

require_once('GeoIp.php');


echo "<h1>GeoIP database versions</h1>";

foreach (GeoIp::getDatabaseVersions() as $d)
{
    echo '<h2>'.$d['name'].' '.$d['version'].'</h2>';
    echo 'Date: '.$d['date'].' ('.ago($d['date']).')<br/>';
    echo $d['file'].'<br/>';
    echo '<br/>';
}


?>
