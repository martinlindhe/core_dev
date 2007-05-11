<?
	require_once('config.php');

	if (!$l) die;	//user not logged in

	require('design_head.php');
?>

	DIN MAIL:<br/>
	<br/>
	
	<a href="mail_new.php">SKRIV NYTT MAIL</a><br/>
	<br/>

<?
	$tot_cnt = mailInboxCount();
	$pager = makePager($tot_cnt, 5);

	$list = mailInboxContent($pager['index'], $pager['items_per_page']);
	//print_r($list);

	foreach($list as $row) {
		echo ($row['user_read']?'<img src="gfx/icon_mail_opened.png" alt="Läst" title="Läst" width="16" height="16"/> ':'<img src="gfx/icon_mail_unread.png" alt="Oläst" title="Oläst" width="16" height="16"/> ');

		$rubrik = $row['sent_ttl'];
		if (!$rubrik) $rubrik = '(ingen rubrik)';
		echo '<a href="mail_read.php?id='.$row['main_id'].'">'.$rubrik.'</a>';
		
		$from_alias = $user->getuser($row['sender_id']);
		$from_alias = $row['sender_id'] ? '<a href="user.php?id='.$row['sender_id'].'">'.$from_alias['u_alias'].'</a>' : 'SYSTEM';
		echo ' från '.$from_alias.' ';
		echo nicedate($row['sent_date']).'<br/>';
	}

	echo '<br/>'.$pager['head'];
	
	require('design_foot.php');
?>