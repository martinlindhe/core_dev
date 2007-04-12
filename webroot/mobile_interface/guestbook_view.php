<?
	require('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$_id = $_GET['id'];
	
	$gb = gbGetById($_id);
	if (!$gb) die;
	
	gbMarkAsRead($_id);

	require('design_head.php');
	
	//print_r($gb);
	
	echo 'DIN GÄSTBOK<br/><br/>';
	
	echo ($gb['user_read']?'Läst':'Oläst').' inlägg:<br/>';

	echo 'Från '.$gb['u_alias'].', '.$gb['sent_date'].'<br/>';
	echo $gb['sent_cmt'].'<br/><br/>';
	echo '<a href="guestbook_reply.php?id='.$gb['sender_id'].'">SVARA</a><br/>';
	echo '<a href="guestbook_history.php?id='.$gb['sender_id'].'">SE HISTORIK</a>';

	require('design_foot.php');
?>