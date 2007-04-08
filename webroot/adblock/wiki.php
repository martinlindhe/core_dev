<?
	require_once('config.php');

	require('design_head.php');

	$wiki = '';
	foreach($_GET as $key => $val) {
		if (substr($key, 0, 5) == 'View:') {
			$wiki = substr($key, 5);
			break;
		}
	}

	$check = getInfoField($wiki);
	if ($check) {
		echo $check;
	} else {
		echo 'This page does not exist!';
	}

	require('design_foot.php');
?>