<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('prop_Url.php');

$u = new Url('http://www.server.com:80/test/path?ofdoom=2');
if ($u->get() != 'http://www.server.com/test/path?ofdoom=2') echo "FAIL 1\n";	//testcase: verify protocol default port is not shown

?>
