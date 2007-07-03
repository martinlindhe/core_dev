<?
	require_once('config.php');
	if (!$l) die;	//user not logged in

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
			foreach($list as $row) {
				echo 'Du vill bli <b>'.$row['sent_cmt'].'</b> med '.getstringMobile($row['main_id']).', skickat '.$row['sent_date'];
				echo ' <a href="?remove='.$row['id_id'].'">RADERA</a><br/>';
			}
			echo '<br/>';
		}
	
		$list = getRelationRequestsToMe();
		if (count($list)) {
			echo 'OBESVARADE FÖRFRÅGNINGAR<br/>';
			foreach($list as $row) {
				echo getstringMobile($row['main_id']).' vill bli <b>'.$row['sent_cmt'].'</b> med dig, skickat '.$row['sent_date'];
				echo ' <a href="?accept='.$row['main_id'].'">ACCEPTERA</a> ';
				echo '<a href="?remove='.$row['id_id'].'">RADERA</a><br/>';
			}
			echo '<br/>';
		}
	}

	if ($_id == $l['id_id']) echo '<div class="h_friends"></div>';
	else {
		echo $user->getstringMobile($_id).' VÄNNER<br/>';
	}
	
	$tot_cnt = getRelationsCount($_id);
	$pager = makePager($tot_cnt, 10);

	$list = getRelations($_id, 'u.u_alias ASC', $pager['index'], $pager['items_per_page']);

	echo '<div class="mid_content">';
	foreach($list as $row)
	{
		echo $user->getstringMobile($row['id_id']). '<br/>';

		echo '<a href="mail_new.php?id='.$row['id_id'].'"><img src="gfx/q_mail.png" alt="Mail"/></a> ';
		echo '<a href="gb_write.php?id='.$row['id_id'].'"><img src="gfx/q_gb.png" alt="Gästbok"/></a> ';

		if ($_id == $l['id_id']) echo '<a href="?remove='.$row['id_id'].'"><img src="gfx/q_delete.png" alt="Ta bort"/></a>';
		echo '<br/>';
	}
	echo '</div>';

	echo $pager['head'];

	require('design_foot.php');
?>