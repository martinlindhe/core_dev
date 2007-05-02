<?
	require_once('config.php');

	if (!$l) die;	//user not logged in

	require('design_head.php');

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $_id = $_GET['id'];
	else $_id = $l['id_id'];

	if ($_id == $l['id_id']) {
		echo 'DIN GÄSTBOK<br/><br/>';
	} else {
		$user_data = $user->getuser($_id);
		echo $user_data['u_alias'].'s GÄSTBOK<br/><br/>';
	}
	
	$tot_cnt = gbCountMsgByUserId($_id);
	$pager = makePager($tot_cnt, 5);

	$list = gbList($_id, $pager['index'], $pager['items_per_page']);
	
	echo $pager['head'].'<br>';

	//print_r($list);
	foreach($list as $row)
	{
		if ($_id == $l['id_id']) echo ($row['user_read']?'läst ':'oläst ');
		echo 'Från '.$row['u_alias'].', '.$row['sent_date'];
		
		$text = substr($row['sent_cmt'], 0, 15);
		if (!$text) $text = '(ingen text)';
		echo '<a href="gb_view.php?id='.$row['main_id'].'">'.$text.'</a>';
		if (strlen($text) < strlen($row['sent_cmt'])) echo '...';
		echo '<br/>';
	}
	
	if ($_id != $l['id_id']) {
		echo '<a href="gb_write.php?id='.$_id.'">SKRIV INLÄGG</a>';
	}

	require('design_foot.php');
?>