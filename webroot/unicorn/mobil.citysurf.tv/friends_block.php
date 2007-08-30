<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];
	
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');

	blockRelation($_id);

	echo 'DU HAR NU BLOCKERAT ANVÄNDAREN<br/>';
	echo '<a href="index.php">TILLBAKA</a>';

	require('design_foot.php');
?>
