<?
	require('config.php');

	require('design_head.php');
?>

	DIN MAIL:<br/>
	<br/>
	
	<a href="mail_new.php">SKRIV NYTT MAIL</a><br/>
	<br/>

<?

	$list = mailInboxContent(0, 5);
	//print_r($list);
	
	for ($i=0; $i<count($list); $i++) {
		if ($list[$i]['user_read']) echo '(läst) '; else echo '(oläst )';
		echo '<a href="mail_read.php?id='.$list[$i]['main_id'].'">'.$list[$i]['sent_ttl'].'</a>';
		echo ' från '.$user->getuser($list[$i]['sender_id']).'<br/>';
	}
	
	require('design_foot.php');
?>