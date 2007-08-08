<?
	require_once('find_config.php');

	if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('bad');

	createXHTMLHeader();
?>
<body style="background-color: #D80911;">
<?
	echo '<center>';
	showComments(COMMENT_IMAGE, $_GET['i'], 20, 3);
	echo '</center>';
?>