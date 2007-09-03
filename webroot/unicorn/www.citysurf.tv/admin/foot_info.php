<?
	//ajax callback script
	require_once('find_config.php');

	$cha_id = '';
	$cha_c = $db->getOneItem("SELECT COUNT(DISTINCT(sender_id)) FROM s_adminchat WHERE user_id = '".$user->id."' AND user_read = '0'");
	if(!$isCrew && !empty($_SESSION['u_a'][0])) {
		$city = explode(',', $_SESSION['u_a'][0]);
	}
	if(!empty($city) && count($city)) {
		$arr = array();
		foreach($city as $v) {
			$arr[] = "a.p_city = '".$v."'";
		}
		$arr = implode(' OR ', $arr);
	} else $arr = false;
	$gb_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_tho') !== false) ? $db->getOneItem("SELECT COUNT(*) FROM s_thought a WHERE ".($arr?'('.$arr.') AND ':'')." view_id = '0' AND status_id = '0'") : 0;
	$pic_c = 0;
	$pht_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pho') !== false) ? $db->getOneItem("SELECT COUNT(*) FROM s_userphoto a INNER JOIN s_user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.view_id = '0' AND a.status_id = '1'") : 0;
	$mv_c = 0;
	$uph_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pimg') !== false) ? $db->getOneItem("SELECT COUNT(*) FROM s_userpicvalid a INNER JOIN s_user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.status_id = '1'") : 0;
	$scc_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false) ? $db->getOneItem("SELECT COUNT(*) FROM s_contribute a WHERE a.status_id = '0'") : 0;
	$scg_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false) ? $db->getOneItem("SELECT COUNT(*) FROM s_contribute a WHERE a.status_id = '1' AND a.con_onday = NOW()") : 0;

	/*
	$kick = $db->getOneItem("SELECT kick_now FROM s_admin WHERE main_id = '".$user->id."'");
	if ($kick) {
		$db->update("UPDATE s_admin SET kick_now = '0', u_date = '".timeout('10 MINUTES')."' WHERE main_id = '".$user->id."'");
		$_SESSION['u_i'] = 0;
		$_SESSION['c_i'] = 0;
		die('.');
	}
	*/
	$db->update("UPDATE s_admin SET u_date = NOW() WHERE main_id = '".$user->id."'");

	$s = $db->getArray("SELECT user_name, main_id FROM s_admin WHERE u_date > '".timeout()."' ORDER BY user_name");
	$o = array();
	foreach ($s as $row) {
		if($row['main_id'] == $user->id) {
			$o[] = strtoupper($row['user_name']);
		} else {
			if(strtolower($row['user_name']) != 'demo')
				$o[] = '<a href="javascript:void(0);" onclick="javascript:makePop(\'user_chat.php?id='.$row['main_id'].'\', \'MSG_'.$row['main_id'].'\', \'\', \'\', 1);" class="txt_look">'.strtoupper($row['user_name']).'</a>'.($isCrew?' [<a href="top.php?k='.$row['main_id'].'&k1='.$row['user_name'].'" target="'.FRS.'head">X</a>]':'');
		}
	}
	$c_str = array();

	if($cha_c > 0) {
		$c_str[] = 'c'.$cha_c;
		$cha_id = $db->getOneItem("SELECT sender_id FROM s_adminchat WHERE user_id = '".$user->id."' AND user_read = '0' ORDER BY sent_date ASC LIMIT 1");
	}
	if($gb_c > 0) {
		$c_str[] = 'tt'.$gb_c;
	}
	if($pic_c > 0) {
		$c_str[] = 'vk'.$pic_c;
	}
	if($scg_c > 0) {
		$c_str[] = 'visi';
	}
	if($scc_c > 0) {
		$c_str[] = 'vis'.$scc_c;
	}
	if($pht_c > 0) {
		$c_str[] = 'fo'.$pht_c;
	}
	if($mv_c > 0) {
		$c_str[] = 'fk'.$mv_c;
	}
	if($uph_c > 0) {
		$c_str[] = 'pb'.$uph_c;
	}
	$c_str = implode(' ', $c_str);
?><?=(($cha_c + $gb_c + $scc_c + $scg_c + $pic_c + $pht_c + $mv_c + $uph_c) > 0)?$c_str.' | ':'';?>;;<?=$cha_c?>;;<?=$cha_id?>;;<?=$gb_c?>;;<?=$pic_c?>;;<?=$pht_c?>;;<?=$mv_c?>;;<?=$uph_c?>;;<?=$scc_c?>;;<?=$scg_c?>;;<?=implode(', ', $o);?>
