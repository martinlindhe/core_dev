<?
	//todo: paging

	//id = userId på den andra personen vi vill se historik med
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];

	require_once('config.php');

	if (!$l) die;	//user not logged in

	require('design_head.php');
	
	$list = gbHistory($l['id_id'], $_id);

	echo 'GÄSTBOK<br/><br/>';
	echo 'Historik med '.$user->getstringMobile($_id).'<br/><br/>';

	if (!count($list)) {
		echo 'Ingen historik finns.';
	} else {
		echo '<div class="mid_content">';
		foreach ($list as $row) {
			echo $user->getstringMobile($row['sender_id']).'<br/>';
			echo 'skrev '.$row['sent_date'].'<br/>';
			echo $row['sent_cmt'].'<br/><br/>';
		}
		echo '</div>';
	}

	require('design_foot.php');
?>