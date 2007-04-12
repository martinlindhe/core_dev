<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

	require('design_head.php');
	
	if (!empty($_GET['remove'])) {
		unblockRelation($_GET['remove']);
	}
	
	echo 'DINA BLOCKERINGAR<br/>';
	
	$list = getBlockedRelations();
	if (count($list)) {
		//print_r($list);

		for ($i=0; $i<count($list); $i++)
		{
			echo '<a href="user.php?id='.$list[$i]['id_id'].'">'.$list[$i]['u_alias'].'</a> K47 ';
			echo '<a href="?remove='.$list[$i]['id_id'].'">HÄV BLOCKERING</a><br/>';
		}
	} else {
		echo 'Du har inga blockeringar.';
	}

	require('design_foot.php');
?>