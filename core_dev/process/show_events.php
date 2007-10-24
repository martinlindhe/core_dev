<?
	require_once('config.php');

	require('design_head.php');

	wiki('ProcessShowEvents');

	$list = getEvents(50);
	if (!empty($list)) {
		d($list);
	} else {
		echo 'Event log is empty';
	}

	require('design_foot.php');
?>