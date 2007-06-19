<?
	$profile = $user->getcontent($s['id_id'], 'user_profile');
	$page = 'view';
	$isFriends = $user->isFriends($s['id_id']);
	if(!$own) {
		$hidden = $user->getinfo($l['id_id'], 'hidden_login');
		if (!$hidden && $isOk) {
			$visit = @$sql->queryUpdate("REPLACE INTO s_uservisit SET visitor_id = '".secureINS($l['id_id'])."', user_id = '".secureINS($s['id_id'])."', status_id = '2', visit_date = NOW()");
			$beenhere = ($visit != '2')?false:true;
		} else if (!$hidden) {
			$visit = @$sql->queryUpdate("REPLACE INTO s_uservisit SET visitor_id = '".secureINS($l['id_id'])."', user_id = '".secureINS($s['id_id'])."', status_id = '1', visit_date = NOW()");
			$beenhere = ($visit != '2')?false:true;
		}
		if (!$hidden && !$beenhere) {
			$c = $user->getinfo($s['id_id'], 'visit_cnt');
			$id = $user->setinfo($s['id_id'], 'visit_cnt', ($c+1));
			if($id[0]) $user->setrel($id[1], 'user_head', $s['id_id']);
		}
	}
	if($own) $user->fix_img();
	define('U_VISIT', true);
	require(DESIGN.'head_user.php');
?>
	<div class="subHead">presentation</div><br class="clr"/>
<?

	if (!empty($profile['user_pres'][1])) {
		echo formatText($profile['user_pres'][1], true);
	} else {
		echo 'Användaren har inte skrivit någon presentation.';
	}

	require(DESIGN.'foot_user.php');
?>
