<?php

require_once('/var/www/core_dev/core/core.php');
require_once('/var/www/core_dev/core/service_recaptcha.php');
require_once('/var/www/core_dev/core/output_xhtml.php');

$pub_key  = '6LfqDQQAAAAAAMF-GaCBYHRJFetLd_BrjO8-2HBW';
$priv_key = '6LfqDQQAAAAAAKOMPfoJYcpqfZBlWQZf1BYiq7qt';

if (recaptchaVerify($priv_key)) {
	echo "correct! saving ".$_POST['var'];
} else {
	echo createXHTMLHeader();

	echo xhtmlForm();
	echo xhtmlInput('var');
	echo recaptchaShow($pub_key);
	echo xhtmlSubmit();
	echo xhtmlFormClose();
}

?>
