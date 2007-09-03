<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('config.php');
	$user->requireLoggedIn();

	$mail = getMail($_GET['id']);
	if (!$mail) die;

	mailMarkAsRead($_GET['id']);

	require('design_head.php');

	echo '<div class="h_mail"></div>';

	if (isset($_GET['delete'])) {
		mailDelete($_GET['id']);
		echo 'Mailet har raderats!<br/>';
		require('design_foot.php');
		die;
	}

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
	
	echo '<a href="mail_read.php?id='.$mail['main_id'].'&delete">RADERA</a><br/>';
	

	require('design_foot.php');
?>
