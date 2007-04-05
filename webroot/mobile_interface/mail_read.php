<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require('config.php');

	$mail = getMail($_GET['id']);
	if (!$mail) die;
	
	require('design_head.php');

	$from_alias = $user->getuser($mail['sender_id']);
	$from_alias = $from_alias['u_alias'];

	echo 'DIN MAIL<br/>';
	echo 'Visar mail från '.$from_alias.', skrivet '.$mail['sent_date'].':<br/>';
	echo '<br/>';
	echo 'Rubrik: '.$mail['sent_ttl'].'<br/>';

	$body = strip_tags($mail['sent_cmt'], '<BR>');

	echo 'Meddelande: '.$body.'<br/>';

	echo '<a href="mail_reply.php?id='.$mail['main_id'].'">SVARA</a>';

	require('design_foot.php');
?>