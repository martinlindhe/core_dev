<?
	/*
		spyGetList()
		ger dig en array med en lista på vad som är bevakat, med typen som är
		bevakad och dessutom med en titel, tex:
		type_id: f				title: "En forumtråd!"
		type_id: b				title: Frans
		type_id: g				title: Frans
		betyder att jag bevakar på ett foruminlägg, samt Frans bilder och blogg.
	*/
	function spyGetList()
	{
		global $sql, $t, $l;
		return $sql->query("
		SELECT s.main_id, s.type_id, s.object_id,
			IF(s.type_id = 'f', f.sent_ttl,
			IF(s.type_id = 'b', b.u_alias,
			IF(s.type_id = 'g', g.u_alias,''))) as title
		FROM s_userspycheck s
			LEFT JOIN s_f f ON s.type_id = 'f' AND f.main_id = s.object_id
			LEFT JOIN s_user b ON s.type_id = 'b' AND b.id_id = s.object_id
			LEFT JOIN s_user g ON s.type_id = 'g' AND g.id_id = s.object_id
		WHERE s.user_id = '".$l['id_id']."'
		ORDER BY s.type_id", 0, 1);
	}

	/*
		spyDelete($_id, $_type)
		tar bort en bevakning
	*/
	function spyDelete($_id, $_type)
	{
		global $sql, $t, $l;
		if (!is_numeric($_id)) return false;
		$_type = addslashes($_type);

		$q = "DELETE FROM s_userspycheck WHERE object_id = '".$_id."' AND type_id = '".$_type."' AND user_id = '".$l['id_id']."' LIMIT 1";
		if ($sql->queryUpdate($q)) return true;
		return false;
	}

	/*
		spyAdd($_id, $_type)
		lägger till en bevakning på objekt-id:t (main_id på s_f, s_userblog och
		s_userphoto) samt $_type (f, g, b)
	*/
	function spyAdd($_id, $_type)
	{
		global $sql, $t, $l;

		if (!is_numeric($_id)) return false;
		$_type = addslashes($_type);

		//kollar ifall bevakning redan finns
		$q = "SELECT COUNT(*) FROM s_userspycheck WHERE object_id = '".$_id."' AND user_id = '".$l['id_id']."' AND type_id = '".$_type."'";
		if ($sql->queryResult($q)) return false;

		return $sql->queryInsert("INSERT INTO s_userspycheck SET object_id = '".$_id."', user_id = '".$l['id_id']."', type_id = '".$_type."'");
	}

	/*
		kollar om det finns en bevakning på aktuellt objekt, returnerar true/false
	*/
	function spyActive($_id, $_type)
	{
		global $sql, $t, $l;

		if (!is_numeric($_id)) return false;
		$_type = addslashes($_type);

		$q = "SELECT COUNT(*) FROM s_userspycheck WHERE object_id=".$_id." AND type_id='".$_type."' AND user_id='".$l['id_id']."'";
		if ($sql->queryResult($q)) return true;
		return false;
	}


	/*
		spyPost($_id, $_type, $_object)
		denna funktion kör du när någon postat info, tex ett foruminlägg eller när
		någon postar ett blogginlägg. kommentar i koden:

		Only execute this function if the media is visible for everyone. private objects should not be posted.
		forum = $_id = top_id/parent_id of the forum thread, so everyone that has spy on will be notified
		gallery = $_id = user id
		blog = $_id = user id
		$_object = is the text-string that should be inserted in the text sent to the user, alias for users and topic for forum
	*/
	function spyPost($_id, $_type, $_object)
	{
		global $sql, $t;

		if (!is_numeric($_id)) return false;
		$_type = addslashes($_type);

		$q = "SELECT s.user_id FROM s_userspycheck s INNER JOIN s_user u ON u.id_id = s.user_id AND u.status_id = '1' WHERE s.type_id = '".$_type."' AND s.object_id = '".$_id."'";
		$res = $sql->query($q);
		//print_r($res);
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

	/*
		spyPostSend($_user, $_title, $_msg)
		skickar ut ett mail. ersätter user-class-funktionen spy().
	*/
	function spyPostSend($_user, $_title, $_msg)
	{
		global $sql, $user, $t;
		$sql->queryInsert("INSERT INTO s_usermail SET
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