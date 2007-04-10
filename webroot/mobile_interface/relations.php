<?
	require_once('config.php');
	require('design_head.php');
	
	if (!empty($_GET['accept'])) {
		acceptRelationRequest($_GET['accept']);
	}
	
	if (!empty($_GET['remove'])) {
		removeRelation($_GET['remove']);
	}

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


	echo 'DINA VÄNNER<br/>';
	$list = getRelations($s['id_id'], 'u.u_alias ASC', 0, 10);

	for ($i=0; $i<count($list); $i++)
	{
		echo '(online/offline) ';
		echo '<b>'.$list[$i]['rel_id'].':</b> ';
		echo '<a href="user.php?id='.$list[$i]['id_id'].'">'.$list[$i]['u_alias'].'</a> K47 ';
		echo '<a href="guestbook_reply.php?id='.$list[$i]['id_id'].'">GÄSTBOK</a> ';
		echo '<a href="mail_new?id='.$list[$i]['id_id'].'">MAILA</a> ';
		echo '<a href="?remove='.$list[$i]['id_id'].'">RADERA</a><br/>';
	}

	require('design_foot.php');
?>