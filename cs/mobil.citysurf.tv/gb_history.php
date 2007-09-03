<?
	//todo: paging

	//id = userId på den andra personen vi vill se historik med
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];

	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
	
	$list = gbHistory($user->id, $_id);

	echo '<div class="h_gb"></div>';
	echo 'Historik med '.$user->getstringMobile($_id).'<br/><br/>';

	if (!count($list)) {
		echo 'Ingen historik finns.';
	} else {
		echo '<div class="mid_content">';
		foreach ($list as $row) {
			echo $user->getstringMobile($row['sender_id']).'<br/>';
			echo 'skrev '.$row['sent_date'].'<br/>';
			echo $row['sent_cmt'].'<br/><br/>';

			if ($row['sender_id'] == $_id) $gb_id = $row['main_id'];
		}
		
		echo '</div>';
		echo '<a href="gb_write.php?id='.$gb_id.'&amp;reply">SVARA</a><br/>';
	}

	require('design_foot.php');
?>
