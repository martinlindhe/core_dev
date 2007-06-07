<?
	/*
		update_vip.php av Martin Lindhe, 2007.06.04
		
		skript som körs regelbundet en gång om dygnet (efter midnatt) och uppdaterar alla användares vip-nivåer
	*/

	$vip_rows = $sql->query('SELECT * FROM s_vip ORDER BY level DESC',0,1);
	foreach ($vip_rows as $vip) {
		if (isset($done[$vip['userId']])) continue;
		if ($vip['level'] > 3) continue;	//level2 = vip, level3=vip delux. resterande är admin, webmaster osv

		echo 'Setting days left for userId '.$vip['userId'].' to '.($vip['days']-1).' (vip level '.$vip['level'].')<br/>';
		if ($vip['days'] >= 1) {
			$q = 'UPDATE s_vip SET days='.($vip['days']-1).' WHERE id='.$vip['id'];
			$sql->queryUpdate($q);
			$curr_viplevel = $vip['level'];
		} else {
			$q = 'DELETE FROM s_vip WHERE id='.$vip['id'];
			$sql->queryUpdate($q);
			
			$q = 'SELECT * FROM s_vip WHERE userId='.$vip['userId'].' ORDER BY level DESC LIMIT 1';
			$new_rows = $sql->query($q, 0, 1);
			if ($new_rows) $curr_viplevel = $new_rows[0]['level'];
			else $curr_viplevel = '1'; //denote to normal user level
		}

		$q = 'UPDATE s_user SET level_id="'.$curr_viplevel.'" WHERE id_id='.$vip['userId'];
		$sql->queryUpdate($q);
		echo 'user level sat to '.$curr_viplevel.'<br/>';

		$done[$vip['userId']] = true;
	}
	die;
?> 