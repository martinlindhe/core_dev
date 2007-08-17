<?
	/*
		tar emot text från quickchat-funktionen och sparar ner i databasen + gör nåt mer crap
	*/
	require_once('config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$id = $_GET['id'];

	$s = $user->getuser($id);

	$isFriends = $user->isFriends($id);

	$closed = false;
	$blocked = false;
	$notall = false;

	$own = (!$closed && $id == $user->id) ? true : false;
	if ($own) die('.');
	if (!$closed && $_SESSION['data']['level_id'] != '10') {
		$q = 'SELECT rel_id FROM s_userblock WHERE user_id = '.$user->id.' AND friend_id = '.$id.' LIMIT 1';
		$blocked = $db->getOneItem($q);
	}
	if (!$isFriends) {
		$onlL = $user->getinfo($user->id, 'private_chat');
		$onlS = $user->getinfo($id, 'private_chat');
		if ($onlL) {
			$notall = true;
			$all = '1';
		} else if ($onlS) {
			$notall = true;
			$all = '2';
		}
	}

	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	if(!$notall && !$blocked && !$closed && !empty($_POST['msg'])) {
		$str = str_replace('%2b', '+', $_POST['msg']);
		$str = substr($str, 0, 250);
		$q = "INSERT INTO s_userchat SET
		sender_id = '".$user->id."',
		user_id = '".$id."',
		sent_cmt = '".$db->escape($str)."',
		sent_date = NOW(),
		user_read = '0'";
		$db->insert($q);
	/*	$c = $user->getinfo($id, 'chat_count');
		if(!$c) $c = 0;
		$id = $user->setinfo($id, 'chat_count', ($c+1));
		if($id[0]) $user->setrel($id[1], 'user_retrieve', $id);
	*/
		exit;
	}
	//$history = (!empty($key))?true:false;

	$history = $user->vip_check(VIP_LEVEL1);
	$history = false;

	if(!$notall && !$closed && !$blocked) {
		if($history && $isOk && !$user->getinfo($user->id, 'hidden_chat')) {
			if($isOk) $his_lim = 6; else $his_lim = 3;
			$his = $sql->query("SELECT c.user_id, u.u_alias, c.sent_date, c.sent_cmt, c.sender_id FROM s_userchat c LEFT JOIN s_user u ON u.id_id = c.sender_id WHERE (c.user_id = '".$user->id."' AND c.sender_id = '".$id."' AND c.user_read = '1') OR (c.sender_id = '".$user->id."' AND c.user_id = '".secureINS($id)."') ORDER BY c.main_id DESC LIMIT $his_lim");
		} else {
			$history = false;
		}
		$q = 'SELECT c.user_id, u.u_alias, c.sent_date, c.sent_cmt, c.sender_id FROM s_userchat c '.
				'LEFT JOIN s_user u ON u.id_id = c.sender_id '.
				'WHERE c.user_id = '.$user->id.' AND c.sender_id = '.$id.' AND c.user_read = "0" '.
				'ORDER BY c.main_id ASC';
		$res = $db->getArray($q);
	} else {
		$history = false;
		$q = 'SELECT c.user_id, CONCAT("Otillgänglig användare", ""), c.sent_date, c.sent_cmt, c.sender_id FROM s_userchat c '.
				'WHERE c.user_id = '.$user->id.' AND c.sender_id = '.$id.' AND c.user_read = "0" '.
				'ORDER BY c.main_id ASC';
		$res = $db->getArray($q);
	}
	$guid = md5($id);#substr($id, 0, 16).'.'.substr($id, 16, 4).'-'.substr($id, 20, 8).'.'.substr($id, 28, 4);
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
		$sql->queryUpdate("UPDATE s_userchat SET user_read = '1' WHERE user_id = '".$user->id."' AND sender_id = '".$id."' AND user_read = '0'");
	} elseif($closed) die(','); elseif($blocked) die(':'); elseif($notall) die(';'.$all);
?> 