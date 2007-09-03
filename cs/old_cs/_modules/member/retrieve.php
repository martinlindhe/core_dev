<?
	if(!$l) die('NAK');
	#$l['id_id'] = '857a398e20e4182211b8f0d1f3fe902f';
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	$info = $user->getcontent($l['id_id'], 'user_retrieve');
	$arr = '';
	$i = 0;
	$str = '';
	$inf = array();
	foreach($info as $item) {
		$arr .= ($i?'&':'').strtoupper(substr($item[0], 0, 1)).'='.$item[1];
		$str .= ($item[0]?$item[0].':'.$item[1].'#':'');
		if($item[0] && $item[1]) $inf[] = $item[0].$item[1];
		$i++;
	}
	$str = str_replace('chat_count', '0', $str);
	$cha_c = $sql->queryResult("SELECT COUNT(DISTINCT(sender_id)) as count FROM {$t}userchat WHERE user_id = '".secureINS($l['id_id'])."' AND user_read = '0'");
	if($cha_c > 0)
		$cha_id = $sql->queryResult("SELECT c.sender_id FROM {$t}userchat c INNER JOIN {$t}user u ON u.id_id = c.sender_id AND u.status_id = '1' WHERE c.user_id = '".secureINS($l['id_id'])."' AND c.user_read = '0' ORDER BY c.sent_date ASC LIMIT 1");
	else $cha_id = '';
	if($cha_id) {
		$str .= 'chat_count:'.$cha_c.':'.$cha_id;
		$arr .= '&C='.$cha_c.'&I='.$cha_id;
		$inf[] = 'c'.$cha_c;
	}
	if($arr != @$_SESSION['data']['cache']) {
		$_SESSION['data']['cache'] = $arr;
	}
	$inf = implode(' ', $inf);
	$_SESSION['data']['cachestr'] = $inf;
	echo $str;
	exit;
/*
	$gb_c = intval($user->getinfo($l['id_id'], 'gb_count'));
	$mail_c = intval($user->getinfo($l['id_id'], 'mail_count'));
	$cha_c = $sql->queryResult("SELECT COUNT(DISTINCT(sender_id)) as count FROM {$tab['chat']} WHERE user_id = '".secureINS($l['id_id'])."' AND user_read = '0'");
	if($cha_c > 0)
		$cha_id = $sql->queryResult("SELECT sender_id FROM {$tab['chat']} WHERE user_id = '".secureINS($l['id_id'])."' AND user_read = '0' ORDER BY sent_date ASC LIMIT 1");
	else $cha_id = '';
	$spy_c = intval($user->getinfo($l['id_id'], 'spy_count'));
	#$spy_c = $sql->queryResult("SELECT COUNT(*) as count FROM {$tab['user']}spycheck WHERE user_id = '".secureINS($l['id_id'])."'");

	$rel_c = $sql->queryResult("SELECT COUNT(*) as count FROM {$tab['relquest']} a INNER JOIN {$tab['user']} u ON u.id_id = a.sender_id AND u.status_id = '1' WHERE a.user_id = '".secureINS($l['id_id'])."' AND a.status_id = '0'");
if(!empty($_GET['oe'])) {
		$r = $user->getinfo($l['id_id'], 'random');
		if(!$r) {
			$sexs = array('M' => 'F', 'F' => 'M', '' => 'F');
			$sexs = $sexs[$l['u_sex']];
		} else {
			if($r == 'B') $sexs = false;
			else $sexs = $r;
		}
		if($sexs) {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$tab['level']} WHERE MATCH(level_id) AGAINST('+VALID +SEX".$sexs."' IN BOOLEAN MODE)");
			$c = mt_rand(0, $c);
			$shad = @$res = $sql->queryResult("SELECT id_id FROM {$tab['level']} WHERE MATCH(level_id) AGAINST('+VALID +SEX".$sexs."' IN BOOLEAN MODE) LIMIT $c, 1");
		} else {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$tab['level']} WHERE MATCH(level_id) AGAINST('+VALID +SEX".$sexs."' IN BOOLEAN MODE)");
			$c = mt_rand(0, $c);
			$shad = @$res = $sql->queryResult("SELECT id_id FROM {$tab['level']} WHERE MATCH(level_id) AGAINST('+VALID' IN BOOLEAN MODE) LIMIT $c, 1");
		}
	if($shad) {
		$shad = $sql->queryLine("SELECT id_id, u_picid, u_picd, u_alias, u_sex, u_birth FROM {$tab['user']} WHERE id_id = '".secureINS($shad)."' LIMIT 1", 1);
		$shad = $shad['id_id'].$shad['u_picid'].$shad['u_picd'].rawurlencode($shad['u_alias'].' '.$sex[$shad['u_sex']].$user->doage($shad['u_birth'], 0));
	}
} else $shad = '';
	$c_str = array();

	if($gb_c > 0) $c_str[] = 'g'.$gb_c;
	if($mail_c > 0) $c_str[] = 'm'.$mail_c;
	if($spy_c > 0) $c_str[] = 'b'.$spy_c;
	if($rel_c > 0) $c_str[] = 'v'.$rel_c;
	if($cha_c > 0) $c_str[] = 'p'.$cha_c;

$c_str = implode(' ', $c_str);

$rel_onl = $sql->query("SELECT rel.friend_id, u.u_alias, u.u_sex, u.u_birth, u.u_picid, u.u_picd, u.level_id FROM {$tab['relation']} rel INNER JOIN {$tab['user']} u ON u.id_id = rel.friend_id AND u.status_id = '1' WHERE rel.user_id = '".secureINS($l['id_id'])."' AND u.account_date > '".$user->timeout(UO)."' ORDER BY u.u_alias LIMIT 11");
$rel_onlc = intval($sql->queryResult("SELECT COUNT(*) as count FROM {$tab['relation']} rel INNER JOIN {$tab['user']} u ON u.id_id = rel.friend_id AND u.status_id = '1' WHERE rel.user_id = '".secureINS($l['id_id'])."' AND u.account_date > '".$user->timeout(UO)."'"));
$rel_o = '';
$i = 0;
foreach($rel_onl as $row) {
	$len = strlen(rawurlencode($row[1]));
	if(strlen($len) == '1') $len = '0'.$len;
	if(!$i) $i = 1; else $rel_o .= ',';
	if($row[6] < 10) $row[6] = '0'.$row[6];
	$rel_o .= $row[0].$row[4].$row[5].$row[6].$len.rawurlencode($row[1]).$sex[$row[2]].$user->doage($row[3], 0).$user->dobirth($row[3]);
}
	echo "$gb_c;$mail_c;$cha_c;$spy_c;$rel_c;$rel_o;$rel_onlc;$cha_id;$c_str;$shad";
	exit;
*/
	exit;
?> 