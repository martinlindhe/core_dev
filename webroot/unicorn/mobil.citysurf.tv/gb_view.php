<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];

	require_once('config.php');
	$user->requireLoggedIn();
	
	$gb = gbGetById($_id);
	if (!$gb) die;
	
	if ($gb['user_id'] == $user->id) gbMarkAsRead($_id);

	require('design_head.php');
	
	if ($gb['user_id'] == $user->id) echo '<div class="h_gb"></div>';
	else {
		echo $user->getstringMobile($gb['user_id']).'s GÄSTBOK<br/><br/>';
	}
	
	if ($gb['user_id'] == $user->id) echo ($gb['user_read']?'Läst':'Oläst').' inlägg:<br/>';

	echo 'Avsändare: '.$user->getstringMobile($gb['sender_id']).'<br/>';
	echo 'Skickat: '.$gb['sent_date'].'<br/><br/>';

	echo '<div class="mid_content">';
	echo $gb['sent_cmt'];
	echo '</div>';

	if ($gb['user_id'] == $user->id) {
		echo '<a href="gb_write.php?id='.$gb['main_id'].'&amp;reply">SVARA</a><br/>';
		echo '<a href="gb_history.php?id='.$gb['sender_id'].'">SE HISTORIK</a><br/>';
	}

	require('design_foot.php');
?>
