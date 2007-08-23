<?
	/*
	gb.fnc.php - Functions for handling guestbooks
	Orginally written by Frans Rosén
	
	*/

	// function to list msg from a userid
	function gbList($user_id, $_start = 0, $_end = 0)
	{
		global $db;
		if (!is_numeric($user_id)) return false;

		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM s_usergb gb LEFT JOIN s_user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.user_id = ".$user_id." AND gb.status_id = '1' ORDER BY gb.main_id DESC";
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;
		return $db->getArray($q);
	}

	// to return a specific guestbook entry by its id. will return false if the user or the message is deleted.
	function gbGetById($msg_id)
	{
		global $db, $user;
		if (!is_numeric($msg_id)) return false;

		$q = "SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM s_usergb gb LEFT JOIN s_user u ON u.id_id = gb.sender_id AND u.status_id = '1' WHERE gb.main_id = ".$msg_id." AND gb.status_id = '1'";
		$result = $db->getOneRow($q);

		//dont return private messages to other ppl
		if ($result['private_id'] && ($user->id != $result['user_id'])) return false;

		return $result;
	}

	// count active msg from a user.
	function gbCountMsgByUserId($user_id)
	{
		global $db;
		if (!is_numeric($user_id)) return false;

		$q = 'SELECT COUNT(*) FROM s_usergb gb WHERE gb.user_id = '.$user_id.' AND gb.status_id = "1"';
		return $db->getOneItem($q);
	}

	//returns the number of unread messages for active user
	function gbCountUnread()
	{
		global $db, $user;

		return $db->getOneItem('SELECT COUNT(*) FROM s_usergb WHERE user_id = '.$user->id.' AND user_read = "0"');
	}

	// returns all msg sent between two users. NEW! separator is inserted because the id's are numeric. for example: before a history between userid 11 and userid 232 would have the users_id = 11232. but that is also the historycode for user 1 and user 1232.
	// therefore the users_id will now be 11-232.
	function gbHistory($first_user, $second_user, $_start = 0, $_end = 0)
	{
		global $db;

		$or = array($first_user, $second_user);
		sort($or);
		$q = 'SELECT gb.*, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM s_usergbhistory h '.
				'INNER JOIN s_usergb gb ON gb.main_id = h.msg_id AND gb.status_id = "1" '.
				'LEFT JOIN s_user u ON u.id_id = gb.sender_id AND u.status_id = "1" '.
				'WHERE h.users_id = "'.implode('-', $or).'" ORDER BY h.msg_id DESC';
		if ($_start || $_end) $q .= ' LIMIT '.$_start.','.$_end;
		return $db->getArray($q);
	}

	// insert new guestbook-msg
	//is_answer = id på gästboksinlägget som detta är ett svar till
	//private = 0 eller 1, om det är ett privat gästboksinlägg
	function gbWrite($msg, $user_id, $is_answer = 0, $private = 0)
	{
		global $db, $user;
		if (!is_numeric($user_id) || !is_numeric($is_answer) || !is_numeric($private)) return false;

		$q = 'INSERT INTO s_usergb SET user_id = '.$user_id.', sender_id = '.$user->id.', private_id = "'.$private.'", status_id = "1", user_read = "0", sent_cmt = "'.$db->escape($msg).'", sent_html = "0", deleted_id = 0, sent_date = NOW()';
		$res = $db->insert($q);

		$or = array($user_id, $user->id);
		sort($or);

		$db->insert("INSERT INTO s_usergbhistory SET users_id = '".implode('-', $or)."', msg_id = '".$res."'");

		if ($is_answer) {
			$db->update("UPDATE s_usergb SET is_answered = '1' WHERE main_id = '".$db->escape($is_answer)."' AND sender_id = '".$user_id."' AND user_id = '".$user->id."' LIMIT 1");
		}

		$user->counterIncrease('gb', $user_id);
		$user->notifyIncrease('gb', $user_id);

		return true;
	}

	// returns true is msg is deleted.
	function gbDelete($msg_id) {
		global $db, $user;
		if (!is_numeric($msg_id)) return false;

		$res = $db->getOneRow("SELECT main_id, status_id, user_id, sender_id, user_read FROM s_usergb WHERE main_id = '".$msg_id."' LIMIT 1");
		if (!empty($res) && $res['status_id'] == '1' && ($res['user_id'] == $user->id || $res['sender_id'] == $user->id)) {
			$db->update("UPDATE s_usergb SET status_id = '2', deleted_id = '".$user->id."', deleted_date = NOW() WHERE main_id = '".$res['main_id']."' LIMIT 1");
			if (!$res['user_read']) $user->notifyDecrease('gb', $res['user_id']);
			$user->counterDecrease('gb', $res['user_id']);
			return true;
		}
		return false;
	}

	// marks the user's msgs to read.
	function gbMarkUnread()
	{
		global $db, $user;
		if(!$user->getinfo($user->id, 'always_unread')) {
			$db->update("UPDATE s_usergb SET user_read = '1' WHERE user_id = '".$user->id."' AND user_read = '0'");
			$user->notifyReset('gb', $user->id);
			$str = @explode('g:', $_SESSION['data']['cachestr']);
			if(@intval(substr($str[1], 0, 1)) > 0) {
				$user->counterSet($user->id);
				$_SESSION['data']['cachestr'] = $user->cachestr();
			}
		}
	}
	
	// marks one guestbook message as read
	function gbMarkAsRead($_id)
	{
		global $db, $user;
		if (!is_numeric($_id)) return false;

		$db->update("UPDATE s_usergb SET user_read = '1' WHERE user_id = '".$user->id."' AND main_id = ".$_id);
	}
	
?>
