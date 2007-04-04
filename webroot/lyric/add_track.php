<?
	include('include_all.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	$record_id = $_GET['id'];
	addTrack($db, $record_id);

	header('Location: show_record.php?id='.$record_id);
?>