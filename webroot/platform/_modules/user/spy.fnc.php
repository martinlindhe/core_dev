<?
	function spyGetList() {
		global $sql, $t, $l;
		return $sql->query("
		SELECT s.main_id, s.type_id, s.object_id,
			IF(s.type_id = 'f', f.sent_ttl,
			IF(s.type_id = 'b', b.u_alias,
			IF(s.type_id = 'g', g.u_alias,''))) as title
		FROM {$t}userspycheck s
			LEFT JOIN {$t}f f ON s.type_id = 'f' AND f.main_id = s.object_id
			LEFT JOIN {$t}user b ON s.type_id = 'b' AND b.id_id = s.object_id
			LEFT JOIN {$t}user g ON s.type_id = 'g' AND g.id_id = s.object_id
		WHERE s.user_id = '".$l['id_id']."'");
	}

	function spyDelete($_id) {
		global $sql, $t, $l;
		if($sql->queryUpdate("DELETE FROM {$t}userspycheck WHERE main_id = '".$_id."' AND user_id = '".$l['id_id']."' LIMIT 1") > 0) {
			return true;
		}
		return false;
	}
	function spyAdd($_id, $_type) {
		global $sql, $t, $l;
		return $sql->queryInsert("INSERT INTO {$t}userspycheck SET object_id = '".$_id."', user_id = '".$l['id_id']."', type_id = '".$_type."'");
	}
	// only execute this function if the media is visible for everyone. private objects should not be posted.
	// forum = $_id = top_id/parent_id of the forum thread, so everyone that has spy on will be notified
	// gallery = $_id = user id
	// blog = $_id = user id
	// $_object = is the text-string that should be inserted in the text sent to the user, alias for users and topic for forum
	function spyPost($_id, $_type, $_object) {
		global $sql, $t;
		$res = $sql->query("SELECT s.user_id FROM {$t}userspycheck s INNER JOIN {$t}user u ON u.id_id = s.user_id AND u.status_id = '1' WHERE s.type_id = '".$_type."' AND s.object_id = '".$_id."'");
		print_r($res);
		$arr = str_replace(array('[object]', '[object_id]'), array($_object, $_id), gettxt('msg_spy_'.$_type));
		$arr = explode('[separator]', $arr);
		$title = trim($arr[0]);
		$msg = trim($arr[1]);
		$msg = str_replace('[object_url]', $arr[2], $msg);
		foreach($res as $row) {
			spyPostSend($row[0], $title, $msg);
		}
		return true;
	}
	function spyPostSend($_user, $_title, $_msg) {
		global $sql, $user, $t;
		$sql->queryInsert("INSERT INTO {$t}usermail SET
		user_id = '".$_user."',
		sender_id = '0',
		status_id = '1',
		sender_status = '2',
		user_read = '0',
		sent_cmt = '".secureINS($_msg)."',
		sent_ttl = '".secureINS($_title)."',
		sent_date = NOW()");
		$user->counterIncrease('mail', $_user);
		$user->notifyIncrease('mail', $_user);
		return true;
	}
?>