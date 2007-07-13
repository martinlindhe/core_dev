<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');

	echo '<div class="h_online"></div>';

	$result = performSearch(false, 0, 0, 5);
	$list = $result['res'];

	echo '<div class="mid_content">';
	foreach ($list as $row)
	{
		//echo '(bild)<br/>';
		echo $user->getstringMobile($row['id_id']).'<br/>';
		echo $row['lastonl_date'].'<br/>';
		echo '<br/>';
	}
	echo '</div>';

	require('design_foot.php');
?>