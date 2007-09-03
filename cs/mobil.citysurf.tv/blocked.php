<?
	require_once('config.php');
	$user->requireLoggedIn();

	require('design_head.php');
	
	if (!empty($_GET['remove'])) {
		unblockRelation($_GET['remove']);
	}
	
	echo 'MINA BLOCKADE<br/>';
	
	$tot_cnt = getBlockedRelationsCnt();
	$pager = makePager($tot_cnt, 5);
	$list = getBlockedRelations($pager['limit']);

	if (count($list))
	{
		echo $pager['head'].'<br/>';
		foreach($list as $row)
		{
			echo $user->getstringMobile($row['id_id']);
			echo ' <a href="?remove='.$row['id_id'].'">HÄV BLOCKERING</a><br/>';
		}
	} else {
		echo 'Du har inga blockade.<br/>';
	}

	require('design_foot.php');
?>
