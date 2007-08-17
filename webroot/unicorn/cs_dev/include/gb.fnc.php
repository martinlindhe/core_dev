<?
	/*
	gb.fnc.php - Functions for handling guestbooks
	Orginally written by Frans Ros�n
	
	*/

	// function to list msg from a userid
	function gbList($user_id, $_start = 0, $_end = 0)
	{
		global $sql;
		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM s_usergb gb LEFT JOIN s_user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.user_id = '".secureINS($user_id)."' AND gb.status_id = '1' ORDER BY gb.main_id DESC";
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;
		return $sql->query($q, 0, 1);
	}

	// to return a specific guestbook entry by its id. will return false if the user or the message is deleted.
	function gbGetById($msg_id)
	{
		global $sql;

		if (!is_numeric($msg_id)) return false;

		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM s_usergb gb LEFT JOIN s_user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.main_id = ".$msg_id." AND gb.status_id = '1'";
		$result = $sql->queryLine($q, 1);

		//dont return private messages to other ppl
		if ($result['private_id'] && ($l['id_id'] != $result['user_id'])) return false;

		return $result;
	}

	// count active msg from a user.
	function gbCountMsgByUserId($user_id)
	{
		global $sql;
		return $sql->queryResult("SELECT COUNT(*) as count FROM s_usergb gb WHERE gb.user_id = '".secureINS($user_id)."' AND gb.status_id = '1'");
	}

	//returns the number of unread messages for active user
	function gbCountUnread()
	{
		global $sql;

		return $sql->queryResult("SELECT COUNT(*) as count FROM s_usergb gb WHERE gb.user_id = ".$l['id_id']." AND gb.user_read = '0'");
	}

	// returns all msg sent between two users. NEW! separator is inserted because the id's are numeric. for example: before a history between userid 11 and userid 232 would have the users_id = 11232. but that is also the historycode for user 1 and user 1232.
	// therefore the users_id will now be 11-232.
	function gbHistory($first_user, $second_user, $_start = 0, $_end = 0)
	{
		global $sql;

		$or = array($first_user, $second_user);
		sort($or);
		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM s_usergbhistory h INNER JOIN s_usergb gb ON gb.main_id = h.msg_id AND gb.status_id = '1' LEFT JOIN s_user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE h.users_id = '".implode('-', $or)."' ORDER BY h.msg_id DESC";
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;
		return $sql->query($q, 0, 1);
	}

	// insert new guestbook-msg
	//is_answer = id p� g�stboksinl�gget som detta �r ett svar till
	//private = 0 eller 1, om det �r ett privat g�stboksinl�gg
	function gbWrite($msg, $user_id, $is_answer = 0, $private = 0)
	{
		global $sql, $user;
		$res = $sql->queryInsert("INSERT INTO s_usergb SET
		user_id = '".$user_id."',
		sender_id = '".$l['id_id']."',
		private_id = '$private',
		status_id = '1',
		user_read = '0',
		sent_cmt = '".secureINS($msg)."',
		sent_html = '0',
		sent_date = NOW()");
		$or = array($user_id, $l['id_id']);
		sort($or);
		$sql->queryInsert("INSERT INTO s_usergbhistory SET users_id = '".implode('-', $or)."', msg_id = '$res'");
		if($is_answer) {
			$sql->queryUpdate("UPDATE s_usergb SET is_answered = '1' WHERE main_id = '".secureINS($is_answer)."' AND sender_id = '".$user_id."' AND user_id = '".$l['id_id']."' LIMIT 1");
		}
		$user->counterIncrease('gb', $user_id);
		$user->notifyIncrease('gb', $user_id);
		return true;
	}

	// returns true is msg is deleted.
	function gbDelete($msg_id) {
		global $sql, $user;
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, sender_id, user_read FROM s_usergb WHERE main_id = '".secureINS($msg_id)."' LIMIT 1");
		if(!empty($res) && count($res) && $res[1] == '1') {
			if($res[2] == $l['id_id'] || $res[3] == $l['id_id']) {
				$sql->queryUpdate("UPDATE s_usergb SET status_id = '2', deleted_id = '".secureINS($l['id_id'])."', deleted_date = NOW() WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				if(!$res[4]) $user->notifyDecrease('gb', $res[2]);
				$user->counterDecrease('gb', $res[2]);
				return true;
			}
		}
		return false;
	}

	// marks the user's msgs to read.
	function gbMarkUnread() {
		global $user, $sql;
		if(!$user->getinfo($l['id_id'], 'always_unread')) {
			$sql->queryUpdate("UPDATE s_usergb SET user_read = '1' WHERE user_id = '".$l['id_id']."' AND user_read = '0'");
			$user->notifyReset('gb', $l['id_id']);
			$str = @explode('g:', $_SESSION['data']['cachestr']);
			if(@intval(substr($str[1], 0, 1)) > 0) {
				$user->counterSet($l['id_id']);
				$_SESSION['data']['cachestr'] = $user->cachestr();
			}
		}
	}
	
	//marks one guestbook message as read
	function gbMarkAsRead($_id)
	{
		global $sql;

		if (!is_numeric($_id)) return false;

		$sql->queryUpdate("UPDATE s_usergb SET user_read = '1' WHERE user_id = '".$l['id_id']."' AND main_id = ".$_id);
	}
	
?>