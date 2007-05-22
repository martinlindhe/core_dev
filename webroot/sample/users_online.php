<?
	require_once('config.php');

	require('design_head.php');

	echo 'Users online (was active in the last '.$session->online_timeout.' time)<br/><br/>';

	$list = getUsersOnline();
	foreach ($list as $row) {
		echo nameLink($row['userId'], $row['userName']).' at '.$row['timeLastLogin'].'<br/>';
	}

	require('design_foot.php');
?>