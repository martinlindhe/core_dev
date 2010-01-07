<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

//XXX: status: not working!?!?

require_once('core.php');
require_once('client_ftp.php');

$f = new ftp('ftp://ftp.sunet.se/');
//$f->setDebug(true);

$dir = $f->getDir('/pub/os/Linux/distributions/slackware/slackware-10.2/');

foreach ($dir as $file) {
	if ($file['is_file']) {
		$get = $file['name'];

		$data = $f->getData($get);
		d($data);
	}
}

die;

?>
