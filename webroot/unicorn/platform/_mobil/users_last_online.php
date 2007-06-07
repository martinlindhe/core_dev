<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	echo 'ANVÄNDARE SENAST ONLINE:<br/><br/>';

	$list = getLastUsersOnline(5);

	echo '<div class="mid_content">';
	foreach ($list as $row)
	{
		//echo '(bild)<br/>';
		echo $user->getstringMobile($row['id_id']).'<br/>';
		echo $row['sess_date'].'<br/>';
		echo '<br/>';
	}
	echo '</div>';

	require('design_foot.php');
?>