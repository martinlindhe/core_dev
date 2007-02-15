<?
	include('include_all.php');

	if (empty($_GET['error'])) die;
	$error = $_GET['error'];

	include('design_head.php');

	echo getInfoField($db, 'page_errorhelp');
	
	echo 'Todo: error lookup of message: '. $error;

	include('design_foot.php');
?>