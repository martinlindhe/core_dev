<?
	require_once('config.php');

	require('design_head.php');

	wiki('ProcessShowQueue');

	$list = array();
	if (!empty($list)) {
		d($list);
	} else {
		echo 'Queue is empty';
	}

	require('design_foot.php');
?>