<?
	require_once('config.php');
	require_core('functions_faq.php');
	require('design_head.php');

	echo '<h1>Frequently Asked Questions</h1>';

	showFAQ();
	echo '<br/>';

	wiki('FAQ info');

	require('design_foot.php');
?>