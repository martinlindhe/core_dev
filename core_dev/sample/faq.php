<?
	require_once('config.php');
	require('design_head.php');

	echo 'Frequently Asked Questions<br/><br/>';

	showFAQ();
	echo '<br/>';

	wiki('Feedback info');

	require('design_foot.php');
?>