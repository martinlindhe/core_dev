<?
	if(!$l) {
		die('.');
	}

	if(empty($_GET['id'])) {
		$s = $l;
	} else {
		$s = (!is_md5($id))?false:$user->getuser($id);
	}

	$closed = false;
	$blocked = false;
	$notall = false;
	if(!$s && is_md5($_GET['id'])) {
		$closed = true;
		$id = $_GET['id'];
	} else $id = $s['id_id'];
	$own = (!$closed && $s['id_id'] == $l['id_id'])?true:false;
	if($own) die('.');
	if(!$closed && $l['level_id'] != '10') {
		$isBlocked = $sql->queryResult("SELECT rel_id FROM {$t}userblock WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($s['id_id'])."' LIMIT 1");
		if($isBlocked) { 
			$blocked = true;
		}
	}
	if(!$isFriends) {
		$onlL = $user->getinfo($l['id_id'], 'private_chat');
		$onlS = $user->getinfo($s['id_id'], 'private_chat');
		if($onlL) { $notall = true; $all = '1'; } elseif($onlS) { $notall = true; $all = '2'; }
	}

	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	if(!$notall && !$blocked && !$closed && !empty($_POST['msg'])) {
		$str = str_replace('%2b', '+', $_POST['msg']);
		$str = substr($str, 0, 250);
		@$sql->queryInsert("INSERT INTO {$t}userchat SET
		sender_id = '".secureINS($l['id_id'])."',
		user_id = '".secureINS($s['id_id'])."',
		sent_cmt = '".secureINS($str)."',
		sent_date = NOW(),
		user_read = '0'");
	/*	$c = $user->getinfo($s['id_id'], 'chat_count');
		if(!$c) $c = 0;
		$id = $user->setinfo($s['id_id'], 'chat_count', "'".($c+1)."'");
		if($id[0]) $user->setrel($id[1], 'user_retrieve', $s['id_id']);
	*/
		exit;
	}
	$history = (!empty($key))?true:false;
	if(!$notall && !$closed && !$blocked) {
		if($history && $isOk && !$user->getinfo($l['id_id'], 'hidden_chat')) {
			if($isOk) $his_lim = 6; else $his_lim = 3;
			$his = $sql->query("SELECT c.user_id, u.u_alias, c.sent_date, c.sent_cmt, c.sender_id FROM {$t}userchat c LEFT JOIN {$t}user u ON u.id_id = c.sender_id WHERE (c.user_id = '".secureINS($l['id_id'])."' AND c.sender_id = '".secureINS($s['id_id'])."' AND c.user_read = '1') OR (c.sender_id = '".secureINS($l['id_id'])."' AND c.user_id = '".secureINS($s['id_id'])."') ORDER BY c.main_id DESC LIMIT $his_lim");
		} else
			$history = false;
		$res = $sql->query("SELECT c.user_id, u.u_alias, c.sent_date, c.sent_cmt, c.sender_id FROM {$t}userchat c LEFT JOIN {$t}user u ON u.id_id = c.sender_id WHERE c.user_id = '".secureINS($l['id_id'])."' AND c.sender_id = '".secureINS($s['id_id'])."' AND c.user_read = '0' ORDER BY c.main_id ASC");
	} else {
		$history = false;
		$res = $sql->query("SELECT c.user_id, CONCAT('Otillgänglig användare', ''), c.sent_date, c.sent_cmt, c.sender_id FROM {$t}userchat c WHERE c.user_id = '".secureINS($l['id_id'])."' AND c.sender_id = '".secureINS($id)."' AND c.user_read = '0' ORDER BY c.main_id ASC");
	}
	$guid = substr($s['id_id'], 0, 16).'.'.substr($s['id_id'], 16, 4).'-'.substr($s['id_id'], 20, 8).'.'.substr($s['id_id'], 28, 4);
	if($history) {
		for($i = count($his)-1; $i >= 0; $i--) {
			$len = strlen(rawurlencode($his[$i][1]));
			if(strlen($len) == '1') $len = '0'.$len;
			$his[$i][2] = secureOUT(rawurlencode(nicedate($his[$i][2])));
			$dlen = strlen($his[$i][2]);
			if(strlen($dlen) == '1') $dlen = '0'.$dlen;
			echo $guid.'1'.$len.rawurlencode($his[$i][1]).$dlen.$his[$i][2].rawurlencode(secureOUT($his[$i][3]));
		}
	}
	foreach($res as $row) {
		$len = strlen(rawurlencode($row[1]));
		if(strlen($len) == '1') $len = '0'.$len;
		$row[2] = secureOUT(rawurlencode(nicedate($row[2])));
		$dlen = strlen($row[2]);
		if(strlen($dlen) == '1') $dlen = '0'.$dlen;
		echo $guid.'0'.$len.rawurlencode($row[1]).$dlen.$row[2].rawurlencode(secureOUT($row[3]));
	}
	if(!empty($res) && count($res)) {
		$sql->queryUpdate("UPDATE {$t}userchat SET user_read = '1' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($id)."' AND user_read = '0'");
	} elseif($closed) die(','); elseif($blocked) die(':'); elseif($notall) die(';'.$all);
?> 