<?php

require_once('/var/www/core_dev/trunk/core/core.php');
require_once('/var/www/core_dev/trunk/core/client_captcha.php');
require_once('/var/www/core_dev/trunk/core/output_xhtml.php');

$captcha = new captcha();
$captcha->setPrivKey('6LfqDQQAAAAAAKOMPfoJYcpqfZBlWQZf1BYiq7qt');
$captcha->setPubKey( '6LfqDQQAAAAAAMF-GaCBYHRJFetLd_BrjO8-2HBW');

if ($captcha->verify()) {
	echo "correct! saving ".$_POST['var'];
} else {

	echo xhtmlForm();
	echo xhtmlInput('var');
	echo $captcha->render();
	echo xhtmlSubmit();
	echo xhtmlFormClose();
}


?>
