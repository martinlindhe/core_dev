<?
	$allowed = array('unsorted', 'ads', 'trackers', 'counters', 'all');
	if (empty($_GET['type']) || !in_array($_GET['type'], $allowed)) die;

	//redirect from old host. a few (3 or 4) ip's are still pulling from the old URL
	header('Location: http://adblockrules.org/download.php?type='.$_GET['type']);
	die;
?>