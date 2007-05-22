<?
	require_once('config.php');

	require('design_head.php');

	echo 'Here is the 50 last users logged in<br/><br/>';

	$list = getUsersLastLoggedIn(50);
	foreach ($list as $row) {
		echo nameLink($row['userId'], $row['userName']).' at '.$row['timeLastLogin'].'<br/>';
	}

	require('design_foot.php');
?>