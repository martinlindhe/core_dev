<?
	require_once('config.php');
	$session->requireLoggedIn();

	require('design_head.php');

	echo '<h1>Scribble</h1>';

	showComments(COMMENT_SCRIBBLE);

	require('design_foot.php');
?>