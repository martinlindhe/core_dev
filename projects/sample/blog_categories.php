<?
	require_once('config.php');
	$session->requireLoggedIn();

	require('design_head.php');

	echo 'Manage blog categories...<br/><br/>';

	echo manageCategoriesDialog(CATEGORY_BLOG);

	require('design_foot.php');
?>