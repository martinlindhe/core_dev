<?php

$session->requireSuperAdmin();

require_once('GeoIp.php');

switch ($this->owner) {
case 'version':
    echo "<h1>GeoIP database versions</h1>";

    foreach (GeoIp::getDatabaseVersions() as $d)
    {
        echo '<h2>'.$d['name'].' '.$d['version'].'</h2>';
        echo 'Date: '.$d['date'].' ('.ago($d['date']).')<br/>';
        echo $d['file'].'<br/>';
        echo '<br/>';
    }

    echo '&raquo; '.ahref('a/geoip/query', 'Query GeoIP database');
    break;

case 'query':

    function queryHandler($p)
    {
        echo '<h2>Result for '.$p['ip'].'</h2>';
        d( GeoIP::getRecord($p['ip']) );

        echo 'Time zone: '. GeoIP::getTimezone($p['ip']).'<br/>';
    }

    echo '<h1>Query GeoIP database</h1>';

    $x = new XhtmlForm();
    $x->addInput('ip', 'IP');
    $x->addSubmit('Lookup');
    $x->setHandler('queryHandler');
    echo $x->render();

    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
