<?
	/*
	gb.fnc.php - Functions for handling guestbooks
	Orginally written by Frans Rosén
	
	*/

	// function to list msg from a userid
	function gbList($user_id, $_start = 0, $_end = 0)
	{
		global $sql, $t;
		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usergb gb LEFT JOIN {$t}user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.user_id = '".secureINS($user_id)."' AND gb.status_id = '1' ORDER BY gb.main_id DESC";
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;
		return $sql->query($q, 0, 1);
	}

	// to return a specific guestbook entry by its id. will return false if the user or the message is deleted.
	//only returns messages beloning to current user
	function gbGetById($msg_id)
	{
		global $sql, $l, $t;

		if (!is_numeric($msg_id)) return false;

		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usergb gb LEFT JOIN {$t}user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.main_id = ".$msg_id." AND gb.user_id = ".$l['id_id']." AND gb.status_id = '1'";
		return $sql->queryLine($q, 1);
	}

	// count active msg from a user.
	function gbCountMsgByUserId($user_id)
	{
		global $sql, $t;
		return $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usergb gb WHERE gb.user_id = '".secureINS($user_id)."' AND gb.status_id = '1'");
	}

	//returns the number of unread messages for active user
	function gbCountUnread()
	{
		global $sql, $t, $l;

		return $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usergb gb WHERE gb.user_id = ".$l['id_id']." AND gb.user_read = '0'");
	}

	// returns all msg sent between two users. NEW! separator is inserted because the id's are numeric. for example: before a history between userid 11 and userid 232 would have the users_id = 11232. but that is also the historycode for user 1 and user 1232.
	// therefore the users_id will now be 11-232.
	function gbHistory($first_user, $second_user, $_start = 0, $_end = 0)
	{
		global $sql, $t;
		sort($or);
		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usergbhistory h INNER JOIN {$t}usergb gb ON gb.main_id = h.msg_id AND gb.status_id = '1' LEFT JOIN {$t}user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE h.users_id = '".implode('-', $or)."' ORDER BY h.main_id DESC";
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;
		return $sql->query($q, 0, 1);
	}

	// insert new guestbook-msg
	function gbWrite($msg, $user_id, $is_answer = false, $private = false)
	{
		global $sql, $user, $t, $l;
		$res = $sql->queryInsert("INSERT INTO {$t}usergb SET
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
		$sql->queryInsert("INSERT INTO {$t}usergbhistory SET users_id = '".implode('-', $or)."', msg_id = '$res'");
		if($is_answer) {
			$sql->queryUpdate("UPDATE {$t}usergb SET is_answered = '1' WHERE main_id = '".secureINS($is_answer)."' AND sender_id = '".$user_id."' AND user_id = '".$l['id_id']."' LIMIT 1");
		}
		$user->counterIncrease('gb', $user_id);
		$user->notifyIncrease('gb', $user_id);
		return true;
	}

	// returns true is msg is deleted.
	function gbDelete($msg_id) {
		global $sql, $t, $l, $user;
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, sender_id, user_read FROM {$t}usergb WHERE main_id = '".secureINS($msg_id)."' LIMIT 1");
		if(!empty($res) && count($res) && $res[1] == '1') {
			if($res[2] == $l['id_id'] || $res[3] == $l['id_id']) {
				$sql->queryUpdate("UPDATE {$t}usergb SET status_id = '2', deleted_id = '".secureINS($l['id_id'])."', deleted_date = NOW() WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				if(!$res[4]) $user->notifyDecrease('gb', $res[2]);
				$user->counterDecrease('gb', $res[2]);
				return true;
			}
		}
		return false;
	}

	// marks the user's msgs to read.
	function gbMarkUnread() {
		global $user, $sql, $l, $t;
		if(!$user->getinfo($l['id_id'], 'always_unread')) {
			$sql->queryUpdate("UPDATE {$t}usergb SET user_read = '1' WHERE user_id = '".$l['id_id']."' AND user_read = '0'");
			$user->notifyReset('gb', $l['id_id']);
			$str = @explode('g:', $_SESSION['data']['cachestr']);
			if(intval(substr($str[1], 0, 1)) > 0) {
				$user->counterSet($l['id_id']);
				$_SESSION['data']['cachestr'] = $user->cachestr();
			}
		}
	}
	
	//marks one guestbook message as read
	function gbMarkAsRead($_id)
	{
		global $sql, $t, $l;

		if (!is_numeric($_id)) return false;

		$sql->queryUpdate("UPDATE {$t}usergb SET user_read = '1' WHERE user_id = '".$l['id_id']."' AND main_id = ".$_id);
	}
	
?>