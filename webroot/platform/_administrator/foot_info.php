<?
session_start();
#ob_start();
#    ob_implicit_flush(0);
 #   ob_start('ob_gzhandler');
#header('Content-Type: text/html; charset=utf8');

	setlocale(LC_TIME, "swedish");
	setlocale(LC_ALL, 'sv_SE.ISO_8859-1');
	require("./set_onl.php");
	if(notallowed()) {
		die('.');
	}
	$sql = &new sql();
	$user = &new user($sql);
#require("./sms_ue231fetch.php");
	$cha_id = '';
	$cha_c = mysql_result(mysql_query("SELECT COUNT(DISTINCT(sender_id)) as count FROM {$t}adminchat WHERE user_id = '".secureINS($_SESSION['u_i'])."' AND user_read = '0'"), 0, 'count');
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
	$gb_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_tho') !== false)?mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}thought a WHERE ".($arr?'('.$arr.') AND ':'')." view_id = '0' AND status_id = '0'"), 0, 'count'):0;
	$pic_c = 0;
	$pht_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pho') !== false)?mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}userphoto a INNER JOIN {$t}user u ON u.id_id = a.user_id AND u.status_id = '1' WHERE a.view_id = '0' AND a.status_id = '1'"), 0, 'count'):0;
	$mv_c = 0;
	$uph_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_pimg') !== false)?mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}userpicvalid a INNER JOIN {$t}user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.status_id = '1'"), 0, 'count'):0;
	$scc_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false)?mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}contribute a WHERE a.status_id = '0'"), 0, 'count'):0;
	$scg_c = ($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false)?(mysql_result(mysql_query("SELECT COUNT(*) as count FROM {$t}contribute a WHERE a.status_id = '1' AND a.con_onday = NOW()"), 0, 'count')?'0':'1'):0;
	$kick = $sql->queryResult("SELECT kick_now FROM {$t}admin WHERE main_id = '".secureINS($_SESSION['u_i'])."'");
	if($kick) {
		mysql_query("UPDATE {$t}admin SET kick_now = '0', u_date = '".timeout('10 MINUTES')."' WHERE main_id = '".secureINS($_SESSION['u_i'])."'");
		$_SESSION['u_i'] = 0;
		$_SESSION['c_i'] = 0;
		die('.');
	} else
		mysql_query("UPDATE {$t}admin SET u_date = NOW() WHERE main_id = '".secureINS($_SESSION['u_i'])."'");

	$s = mysql_query("SELECT user_name, main_id FROM {$t}admin WHERE u_date > '".timeout()."' ORDER BY user_name");
	$o = array();
	while($r = mysql_fetch_row($s)) {
		if($r[1] == $_SESSION['u_i']) {
			$o[] = strtoupper($r[0]);
		} else {
			if(strtolower($r[0]) != 'demo')
				$o[] = '<a href="javascript:void(0);" onclick="javascript:makePop(\'user_chat.php?id='.$r[1].'\', \'MSG_'.$r[1].'\', \'\', \'\', 1);" class="txt_look">'.strtoupper($r[0]).'</a>'.($isCrew?' [<a href="top.php?k='.$r[1].'&k1='.$r[0].'" target="'.FRS.'head">X</a>]':'');
		}
	}
	$c_str = array();

	if($cha_c > 0) {
		$c_str[] = 'c'.$cha_c;
		$cha_id = mysql_result(mysql_query("SELECT sender_id FROM {$t}adminchat WHERE user_id = '".secureINS($_SESSION['u_i'])."' AND user_read = '0' ORDER BY sent_date ASC LIMIT 1"), 0, 'sender_id');
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