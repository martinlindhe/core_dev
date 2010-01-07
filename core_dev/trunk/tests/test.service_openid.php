<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('core.php');
require_once('service_openid.php');

die('XXX: not working');

openidLogin('http://projects.localhost/openid.php');

?>
