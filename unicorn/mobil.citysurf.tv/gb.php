<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $_id = $_GET['id'];
	else $_id = $user->id;

	if ($_id == $user->id) {
		echo '<div class="h_gb"></div>';
	} else {
		$user_data = $user->getuser($_id);
		echo $user_data['u_alias'].'s GÄSTBOK<br/><br/>';
	}
	
	$tot_cnt = gbCountMsgByUserId($_id);
	$pager = makePager($tot_cnt, 5);

	$list = gbList($_id, $pager['index'], $pager['items_per_page']);
	
	if ($_id != $user->id) {
		echo '<a href="gb_write.php?id='.$_id.'">SKRIV INLÄGG</a><br/>';
	}

	echo '<div class="mid_content">';
	foreach($list as $row)
	{
		echo ($row['user_read']?'<img src="gfx/icon_mail_opened.png" alt="Läst" title="Läst" width="16" height="16"/> ':'<img src="gfx/icon_mail_unread.png" alt="Oläst" title="Oläst" width="16" height="16"/> ');
		
		$text = substr($row['sent_cmt'], 0, 15);
		if (!$text) $text = '(ingen text)';
		echo '<a href="gb_view.php?id='.$row['main_id'].'">'.$text.'</a>';
		if (strlen($text) < strlen($row['sent_cmt'])) echo '...';
		echo '<br/>';
		echo 'från '.$user->getstringMobile($row['sender_id']).'<br/>';
	}
	echo '</div>';

	echo $pager['head'].'<br/>';
	
	require('design_foot.php');
?>
