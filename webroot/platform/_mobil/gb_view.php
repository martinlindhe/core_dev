<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];

	require_once('config.php');

	if (!$l) die;	//user not logged in
	
	$gb = gbGetById($_id);
	if (!$gb) die;
	
	if ($gb['user_id'] == $l['id_id']) gbMarkAsRead($_id);

	require('design_head.php');
	
	//print_r($gb);
	if ($gb['user_id'] == $l['id_id']) echo 'DIN GÄSTBOK<br/><br/>';
	else {
		$user_data = $user->getuser($gb['user_id']);
		echo $user_data['u_alias'].'s GÄSTBOK<br/><br/>';
	}
	
	if ($gb['user_id'] == $l['id_id']) echo ($gb['user_read']?'Läst':'Oläst').' inlägg:<br/>';

	echo 'Från '.$gb['u_alias'].', '.$gb['sent_date'].'<br/>';
	echo $gb['sent_cmt'].'<br/><br/>';
	if ($gb['user_id'] == $l['id_id']) {
		echo '<a href="gb_write.php?id='.$gb['main_id'].'&amp;reply">SVARA</a><br/>';
		echo '<a href="gb_history.php?id='.$gb['sender_id'].'">SE HISTORIK</a>';
	}

	require('design_foot.php');
?>