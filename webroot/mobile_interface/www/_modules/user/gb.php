<?
	$his = false;
	#$isOk = $user->level($l['level_id'], 2);
	#$allowed = array('1' => '30', '3' => '60', '5' => '120');
	#if(!$allowed = @$allowed[$l['level_id']]) $allowed = false;
	#if($allowed) {
	#	$deadline = date("Y-m-d H:i:s", strtotime('-'.$allowed.' DAYS'));
	#}
	$allowed = true;
	$page = 'gb';
	if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
		gbDelete($_GET['del_msg']);
	}

	} else if(!empty($_GET['key']) && is_numeric($_GET['key'])) {
		$his = true;
		if($isOk) {
			$limit = 50;
			$c_his = $_GET['key'];
			$or = array($s['id_id'], $_GET['key']);
		} else {
			$limit = 10;
			$c_his = $l['id_id'];
			$or = array($s['id_id'], $l['id_id']);
		}
		$paging = paging(1, $limit);
		$paging['co'] = 1;
		sort($or);
		$res = $sql->query("SELECT ".CH." gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usergbhistory h INNER JOIN {$t}usergb gb ON gb.main_id = h.msg_id AND gb.status_id = '1' LEFT JOIN {$t}user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE h.users_id = '".implode('', $or)."' ORDER BY h.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} else {
		$offset = $user->getinfo($s['id_id'], 'gb_offset');
		$paging = paging(@$_GET['p'], 20);
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usergb gb WHERE gb.user_id = '".secureINS($s['id_id'])."' AND gb.status_id = '1'");
		$ext = $paging['p'] * $paging['limit'];
		$offset = ($offset + $paging['limit']) - $ext;
		$res = $sql->query("SELECT ".CH." gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usergb gb LEFT JOIN {$t}user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.user_id = '".secureINS($s['id_id'])."' AND gb.status_id = '1' ORDER BY gb.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	}
	if($own) {
		if(!$user->getinfo($l['id_id'], 'always_unread')) {
			$sql->queryUpdate("UPDATE {$t}usergb SET user_read = '1' WHERE user_id = '".$l['id_id']."' AND user_read = '0'");
			$user->notifyReset('gb', $l['id_id']);
			//$_SESSION['data']['cache'] = str_replace('G=', '0=', $_SESSION['data']['cache']);
			#if(strpos($_SESSION['data']['cachestr'], 'G=') !== false) $user->get_cache();
			$str = @explode('g:', $_SESSION['data']['cachestr']);
			if(intval(substr($str[1], 0, 1)) > 0) {
				$user->counterSet($l['id_id']);
				$_SESSION['data']['cachestr'] = $user->cachestr();
			}
		}
	}
	if(!$own) define('U_GBWRITE', 1);
	require(DESIGN.'head_user.php');
	$odd = true;
	if(!empty($res) && count($res)) {
	dopaging($paging, l('user', 'gb', $s['id_id']).'p=', '', 'med', ((!$his)?STATSTR:'<a href="'.l('user', 'gb', $s['id_id']).'">tillbaka</a>'));
	foreach($res as $val) {
	if(($own || $his && $c_his == $l['id_id']) && $allowed && $deadline > $val['sent_date']) { echo '<table cellspacing="0" style="width: 658px;"><tr><td class="pdg cnt spac">Meddelandena skrivna tidigare än <b>'.doDate($deadline, 1, 1).'</b> är nedstängda.<br>Du kan välja att uppgradera ditt medlemskap om du vill läsa äldre inlägg.</td></tr></table>'; break; }
	$prv = ($val['private_id'])?1:0;
	$show_answer = (!$val['is_answered'])?false:true;
	if($l['id_id'].$l['id_id'] == $val['user_id'].$val['sender_id']) {
		$arr = array(0, 0, 1, 1, 1, 0, 'skriv');
	} elseif($l['id_id'] == $val['user_id']) {
		if($his) $arr = array(1, 0, 1, 1, 1, 1, 'svara');
		else $arr = array(1, 1, 1, 1, 1, 1, 'svara');
	} elseif($l['id_id'] == $val['sender_id']) {
		if($his) $arr = array(0, 0, 1, 1, 1, 0, '');
		else $arr = array(0, 1, 1, 1, 1, 0, '');
	} else {
		$arr = array(1, 0, 0, (($prv)?0:1), 1, 0, 'skriv');
		$show_answer = true;
	}
	if($isOk) $arr[1] = 1;
	if($isAdmin && $s['id_id'] != $val['sender_id'] && !$his) $arr[1] = 1;
	if($isAdmin) $arr[2] = 1;
	if($val['sender_id'] == 'SYS' || empty($val['id_id'])) {
		$arr[0] = 0;
		$arr[1] = 0;
		$arr[4] = 0;
	}
	if($his) $arr[1] = 0;
	if(!empty($val['extra_info'])) {
		$extra = true;
		$extra_id = $val['extra_info'];
	} else $extra = false;
	$odd = !$odd;
echo '
	<table cellspacing="0" style="width: 596px;'.($odd?'':' background: #ecf1ea;').'">
	<tr><td class="pdg" style="width: 55px;" rowspan="2">'.$user->getimg($val['id_id'].$val['u_picid'].$val['u_picd'].$val['u_sex'], $val['u_picvalid']).'</td><td class="pdg"><h5 class="l">'.((!$his)?'#'.$offset--.'&nbsp;':'').' '.$user->getstring($val, '', array('noimg' => 1)).' - '.nicedate($val['sent_date']).'</h5><div class="r">'.((!$val['user_read'])?' <b>(oläst inlägg)</b>':((!$show_answer)?' [obesvarat inlägg]':'')).(($prv)?' <span class="off"'.(($isAdmin && !$arr[3])?'':'').'>[privat inlägg]</span>':'').'</div><br class="clr" />
	'.(($arr[3])?(($val['sent_html'])?(safeOUT($val['sent_cmt'])):secureOUT($val['sent_cmt'])):'<span class="em"'.(($isAdmin)?' id="msg:'.$val['main_id'].'"':'').'>Privat inlägg</span>').'
	</td></tr>
	<tr><td class="btm rgt pdg">'.(($arr[4])?''.(($arr[0])?'<input type="button" class="btn2_min" onclick="makeGb(\''.$val['id_id'].'\''.(($arr[5])?', \'&a='.$val['main_id'].'\'':'').');" value="'.$arr[6].'" />':'').(($arr[1])?'<input type="button" class="btn2_min" onclick="goLoc(\''.l('user', 'gb', ($val['sender_id'] == $s['id_id']?$val['sender_id']:$val['user_id']), ($val['sender_id'] == $s['id_id']?$val['user_id']:$val['sender_id'])).'\');" value="historia" />':'').'<input type="button" class="btn2_min" onclick="goLoc(\''.l('user', 'gb', $val['id_id']).'\');" value="gästbok " />':'').(($arr[2])?'<input type="button" class="btn2_min" onclick="if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'gb', $s['id_id']).'del_msg='.$val['main_id'].'\');" value="radera" />':'').'</td></tr>
	</table>
';
	}
	dopaging($paging, l('user', 'gb', $s['id_id']).'p=', '', 'med');
	} else {
echo <<<E
	<table cellspacing="0" style="width: 596px;">
	<tr><td class="cnt">Inga gästboksinlägg.</td></tr>
	</table>
E;
	}
echo '</div>';
	require(DESIGN.'foot_user.php');
	require(DESIGN.'foot.php');
?>
