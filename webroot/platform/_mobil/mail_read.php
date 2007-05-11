<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('config.php');

	if (!$l) die;	//user not logged in

	$mail = getMail($_GET['id']);
	if (!$mail) die;
	
	mailMarkAsRead($_GET['id']);

	require('design_head.php');

	$from_alias = $user->getuser($mail['sender_id']);
	$from_alias = $mail['sender_id'] ? '<a href="user.php?id='.$mail['sender_id'].'">'.$from_alias['u_alias'].'</a>' : 'SYSTEM';

	echo 'DIN MAIL<br/>';
	echo 'Visar mail från '.$from_alias.', skrivet '.$mail['sent_date'].':<br/>';
	echo '<br/>';
	echo 'Rubrik: '.$mail['sent_ttl'].'<br/>';

	$body = strip_tags($mail['sent_cmt'], '<BR>');

	echo 'Meddelande: '.$body.'<br/>';

	if ($mail['sender_id']) {
		echo '<a href="mail_reply.php?id='.$mail['main_id'].'">SVARA</a>';
	}

	require('design_foot.php');
?>