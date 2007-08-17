<?
	/*
		Funktioner f�r communityplattformens interna mail-system

		Koden �r fr�n b�rjan skriven av Frans Ros�n, frans@styleform.se
		Uppdaterad av Martin Lindhe, martin@unicorn.tv, 2007-04-04

		todo:
			* verify logged in
			* verify data types according to db structure and remove secureINS() and qoutations where possible
	*/

	function mailDelete($_id)
	{
		global $sql, $user;

		if (!is_numeric($_id)) return false;

		$res = $sql->queryLine("SELECT main_id, status_id, user_id, sender_id, user_read, sender_status FROM s_usermail WHERE main_id = '".secureINS($_id)."' LIMIT 1");
		if (!empty($res) && count($res) && ($res[1] == '1' || $res[5] == '1')) {
			if ($res[2] == $l['id_id'] || $res[3] == $l['id_id']) {
				if ($res[2] == $l['id_id'] && $res[1] == '1') {
					$sql->queryUpdate("UPDATE s_usermail SET status_id = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
					if (!$res[4]) $user->notifyDecrease('mail', $res[2]);
					$user->counterDecrease('mail', $res[2]);
				}
				if ($res[3] == $l['id_id'] && $res[5] == '1') {
					$sql->queryUpdate("UPDATE s_usermail SET sender_status = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				}
			}
			return true;
		}
		return false;
	}
	
	function mailInboxCount()
	{
		global $sql;

		return $sql->queryResult("SELECT COUNT(*) as count FROM s_usermail WHERE user_id = '".secureINS($l['id_id'])."' AND status_id = '1'");
	}
	
	function mailOutboxCount()
	{
		global $sql;

		return $sql->queryResult("SELECT COUNT(*) as count FROM s_usermail WHERE sender_id = '".secureINS($l['id_id'])."' AND sender_status = '1'");
	}
	
	/* Returns an array with current users inbox content */
	function mailInboxContent($_start = 0, $_end = 0)
	{
		global $sql;
		
		if (!is_numeric($_start) || !is_numeric($_end)) return false;

		$q = 'SELECT m.*, u.* FROM '.$t.'usermail m LEFT JOIN '.$t.'user u ON u.id_id = m.sender_id AND u.status_id = "1" WHERE m.user_id = "'.secureINS($l['id_id']).'" AND m.status_id = "1" ORDER BY m.sent_date DESC';
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;

		return $sql->query($q, 0, 1);
	}
	
	function mailOutboxContent($_start = 0, $_end = 0)
	{
		global $sql;

		if (!is_numeric($_start) || !is_numeric($_end)) return false;

		$q = 'SELECT m.*, u.* FROM '.$t.'usermail m LEFT JOIN '.$t.'user u ON u.id_id = m.user_id AND u.status_id = "1" WHERE m.sender_id = "'.secureINS($l['id_id']).'" AND m.sender_status = "1" ORDER BY m.sent_date DESC';
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;

		return $sql->query($q, 0, 1);
	}
	
	function mailDeleteArray($_arr)
	{
		global $sql, $user, $isAdmin, $s;

		if (!is_array($_arr) || !count($_arr)) return false;
		
		foreach ($_arr as $val) {
			$res = $sql->queryLine("SELECT main_id, status_id, user_id, sender_id, user_read, sender_status FROM s_usermail WHERE main_id = '".secureINS($val)."' LIMIT 1");
			if (!empty($res) && count($res) && ($res[1] == '1' || $res[5] == '1')) {
				if ($isAdmin || $res[2] == $l['id_id'] || $res[3] == $l['id_id']) {
					if($res[2] == $l['id_id']) {

						//if status_id=1
						
						if($res[1] == '1') {
							//if ($_SERVER['REMOTE_ADDR'] == '213.80.11.162') echo 'counterdec';
							$user->counterDecrease('mail', $l['id_id']);
						}

						$sql->queryUpdate("UPDATE s_usermail SET status_id = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
					} elseif($res[3] == $l['id_id']) {
						$sql->queryUpdate("UPDATE s_usermail SET sender_status = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
					} else {
						if($res[1] == '1') $user->counterDecrease('mail', $res[2]);
						if($res[5] == '1') $user->counterDecrease('mail', $res[3]);
						$sql->queryUpdate("UPDATE s_usermail SET status_id = '2', sender_status = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
					}
					if(!$res[4]) {
						//if ($_SERVER['REMOTE_ADDR'] == '213.80.11.162') echo 'notifydec';
						$user->notifyDecrease('mail', $s['id_id']);
					}
				}
			}
		}		

		return true;
	}
	
	function getMail($_id)
	{
		global $sql;
		if (!is_numeric($_id)) return false;

		return $sql->queryLine("SELECT * FROM s_usermail WHERE main_id = ".$_id." LIMIT 1", 1);
	}
	
	function getUnreadMailCount()
	{
		global $sql, $user;

		if (!$l['id_id']) return 0;

		$q = "SELECT COUNT(*) FROM s_usermail WHERE user_id = ".$l['id_id']." AND user_read = '0' AND status_id='1'";
		return $sql->queryResult($q);
	}

	function mailMarkAsRead($_id)
	{
		global $sql, $user;
		
		if (!is_numeric($_id)) return false;

		$user->notifyDecrease('mail', $l['id_id']);
		$sql->queryUpdate("UPDATE s_usermail SET user_read = '1' WHERE main_id = '".$_id."' LIMIT 1");
		
		return true;
	}
	
	//todo: flytta denna funktion till user klassen
	function getUserIdFromAlias($_alias)
	{
		global $sql;

		return $sql->queryResult("SELECT id_id FROM s_user WHERE u_alias = '".secureINS($_alias)."' AND status_id = '1' LIMIT 1");
	}

	function getUserName($_id)
	{
		global $sql;
		if (!is_numeric($_id)) return;

		return $sql->queryResult('SELECT u_alias FROM s_user WHERE id_id = '.$_id.' AND status_id = "1" LIMIT 1');
	}

	//todo: flytta till user klassen
	function getUserFriends()
	{
		global $sql;

		return $sql->query("SELECT rel.main_id, rel.user_id, rel.rel_id, u.id_id, u.u_alias, u.u_picvalid, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth FROM s_userrelation rel RIGHT JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = '1' WHERE rel.user_id = '".secureINS($l['id_id'])."' ORDER BY u.u_alias ASC", 0, 1);
	}

	function sendMail($_to_name, $_cc_name, $_title, $_text, $allowed_html = '', $is_answer = false)
	{
		global $sql, $user;
		
		$_text = strip_tags($_text, $allowed_html);
		$ins_to = getUserIdFromAlias($_to_name);
		if (!$ins_to) {
			return 'Felaktig mottagare!';
		} else if($ins_to == $l['id_id']) {
			return 'Du kan inte skicka till dig sj�lv.';
		}
		/*if(!$isAdmin) {
			$isBlocked = $sql->queryResult("SELECT rel_id FROM s_block WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($ins_to)."' LIMIT 1");
			if($isBlocked) { if($isBlocked == 'u') popupACT('Du har blockerat personen.'); else popupACT('Du �r blockerad.'); }
		}*/
		if (!empty($_cc_name)) {
			$ins_cc = getUserIdFromAlias($_cc_name);
			if($ins_cc && $ins_cc != $l['id_id'] && $ins_cc != $ins_to) {
				if (!$user->blocked($ins_cc, 3)) {
					$res = $sql->queryInsert("INSERT INTO s_usermail SET
					user_id = '".$ins_cc."',
					sender_id = '".$l['id_id']."',
					status_id = '1',
					sender_status = '1',
					user_read = '0',
					sent_cmt = '".secureINS($_text)."',
					sent_ttl = '".secureINS($_title)."',
					sent_date = NOW()");
					$user->counterIncrease('mail', $ins_cc);
					$user->notifyIncrease('mail', $ins_cc);
				}
			}
		}
		
		$res = $sql->queryInsert("INSERT INTO s_usermail SET
		user_id = '".$ins_to."',
		sender_id = '".$l['id_id']."',
		status_id = '1',
		sender_status = '1',
		user_read = '0',
		sent_cmt = '".secureINS($_text)."',
		sent_ttl = '".secureINS($_title)."',
		sent_date = NOW()");

		$user->counterIncrease('mail', $ins_to);
		$user->notifyIncrease('mail', $ins_to);
		
		if ($is_answer) {
			//uppdatera befintligt mail med info om att det �r besvarat
			
			$q = 'UPDATE s_usermail SET is_answered="1" WHERE user_id="'.$l['id_id'].'" AND main_id="'.$is_answer.'"';
			$sql->queryInsert($q);
		}

		return true;
	}

?>