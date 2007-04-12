<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	echo 'ANVÄNDARE SENAST ONLINE:<br/><br/>';

	$list = getLastUsersOnline(4);

	//print_r($list);
	foreach ($list as $row)
	{
		echo '(bild)<br/>';
		echo $row['u_alias'].' K30<br/>';
		echo $row['account_date'].'<br/>';
		echo '<br/>';
	}

	require('design_foot.php');
?>