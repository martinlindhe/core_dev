<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('config.php');

	if (!$l) die;	//user not logged in

	$mail = getMail($_GET['id']);
	if (!$mail) die;
	
	mailMarkAsRead($_GET['id']);

	require('design_head.php');

	echo 'DIN MAIL<br/>';
	echo '<br/>';
	echo 'Rubrik: '.($mail['sent_ttl']?$mail['sent_ttl']:'(ingen rubrik)').'<br/>';
	echo 'Avsändare: '.$user->getstringMobile($mail['sender_id']).'<br/>';
	echo 'Skrivet: '.$mail['sent_date'].'<br/><br/>';

	$body = strip_tags($mail['sent_cmt'], '<BR>');

	echo '<div class="mid_content">';
	echo $body;
	echo '</div>';

	if ($mail['sender_id']) {
		echo '<a href="mail_reply.php?id='.$mail['main_id'].'">SVARA</a><br/>';
	}
	

	require('design_foot.php');
?>