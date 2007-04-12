<?
	require_once('config.php');
	require('design_head.php');

	//user ID to show friend list for
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) $_id = $l['id_id'];
	else $_id = $_GET['id'];
	
	if (!empty($_GET['accept'])) {
		acceptRelationRequest($_GET['accept']);
	}

	if ($_id == $l['id_id'] && !empty($_GET['remove'])) {
		if (isset($_GET['ok'])) {
			removeRelation($_GET['remove']);
		} else {
			echo 'Är du säker på att du vill ta bort denna kompis-relation?<br/><br/>';
			echo '<a href="friends.php?remove='.$_GET['remove'].'&amp;ok">Ja</a><br/><br/>';
			echo '<a href="friends.php">Nej</a>';
			require('design_foot.php');
			die;
		}
	}

	if ($_id == $l['id_id']) {
		$list = getRelationRequestsFromMe();
		if (count($list)) {
			echo 'DU VÄNTAR SVAR FRÅN<br/>';
			for ($i=0; $i<count($list); $i++) {
				echo 'Du vill bli <b>'.$list[$i]['sent_cmt'].'</b> med '.$list[$i]['u_alias'].', skickat '.$list[$i]['sent_date'];
				echo ' <a href="?remove='.$list[$i]['id_id'].'">RADERA</a><br/>';
			}
			echo '<br/>';
		}
	
		$list = getRelationRequestsToMe();
		if (count($list)) {
			echo 'OBESVARADE FÖRFRÅGNINGAR<br/>';
			for ($i=0; $i<count($list); $i++) {
				echo $list[$i]['u_alias'].' vill bli <b>'.$list[$i]['sent_cmt'].'</b> med dig, skickat '.$list[$i]['sent_date'];
				echo ' <a href="?accept='.$list[$i]['main_id'].'">ACCEPTERA</a> ';
				echo '<a href="?remove='.$list[$i]['id_id'].'">RADERA</a><br/>';
			}
			echo '<br/>';
		}
	}

	if ($_id == $l['id_id']) echo 'DINA VÄNNER<br/>';
	else {
		$user_data = $user->getuser($_id);
		echo $user_data['u_alias'].'s VÄNNER<br/>';
	}
	$list = getRelations($_id, 'u.u_alias ASC', 0, 10);

	for ($i=0; $i<count($list); $i++)
	{
		echo '(online/offline) ';
		echo '<b>'.$list[$i]['rel_id'].':</b> ';
		echo '<a href="user.php?id='.$list[$i]['id_id'].'">'.$list[$i]['u_alias'].'</a> K47 ';
		echo '<a href="gb_write.php?id='.$list[$i]['id_id'].'">GÄSTBOK</a> ';
		echo '<a href="mail_new.php?id='.$list[$i]['id_id'].'">MAILA</a> ';
		if ($_id == $l['id_id']) echo '<a href="?remove='.$list[$i]['id_id'].'">RADERA</a>';
		echo '<br/>';
	}

	require('design_foot.php');
?>