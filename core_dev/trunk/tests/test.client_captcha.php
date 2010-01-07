<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

die("XXX: captcha is working, but cant be autotested like this");

require_once('core.php');
require_once('client_captcha.php');
require_once('output_xhtml.php');

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
