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
	
	echo $pager['head'].'<br/>';
	
	for ($i=0; $i<count($list); $i++) {
		if ($list[$i]['user_read']) echo '(läst) '; else echo '(oläst )';
		$rubrik = $list[$i]['sent_ttl'];
		if (!$rubrik) $rubrik = '(ingen rubrik)';
		echo '<a href="mail_read.php?id='.$list[$i]['main_id'].'">'.$rubrik.'</a>';
		
		$from_alias = $user->getuser($list[$i]['sender_id']);
		$from_alias = $from_alias['u_alias'];
		echo ' från <a href="user.php?id='.$list[$i]['sender_id'].'">'.$from_alias.'</a> ';
		
		echo nicedate($list[$i]['sent_date']).'<br/>';
		
	}
	
	require('design_foot.php');
?>