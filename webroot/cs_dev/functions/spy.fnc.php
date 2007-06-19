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
		global $db, $l;
		
		$q = 'SELECT s.main_id, s.type_id, s.object_id,'.
					'IF(s.type_id = "f", f.sent_ttl,'.
					'IF(s.type_id = "b", b.u_alias,'.
					'IF(s.type_id = "g", g.u_alias,""))) as title '.
					'FROM s_userspycheck s '.
						'LEFT JOIN s_f f ON s.type_id = "f" AND f.main_id = s.object_id '.
						'LEFT JOIN s_user b ON s.type_id = "b" AND b.id_id = s.object_id '.
						'LEFT JOIN s_user g ON s.type_id = "g" AND g.id_id = s.object_id '.
						'WHERE s.user_id = '.$l['id_id'].' ORDER BY s.type_id';
		return $db->getArray($q);
	}

	/*
		spyDelete($_id, $_type)
		tar bort en bevakning
	*/
	function spyDelete($_id, $_type)
	{
		global $db, $l;
		if (!is_numeric($_id)) return false;

		$q = 'DELETE FROM s_userspycheck WHERE object_id = '.$_id.' AND type_id = "'.$db->escape($_type).'" AND user_id = '.$l['id_id'].' LIMIT 1';
		return $db->delete($q);
	}

	/*
		spyAdd($_id, $_type)
		lägger till en bevakning på objekt-id:t (main_id på s_f, s_userblog och
		s_userphoto) samt $_type (f, g, b)
	*/
	function spyAdd($_id, $_type)
	{
		global $db, $l;

		if (!is_numeric($_id)) return false;

		//kollar ifall bevakning redan finns
		$q = 'SELECT COUNT(*) FROM s_userspycheck WHERE object_id = '.$_id.' AND user_id = '.$l['id_id'].' AND type_id = "'.$db->escape($_type).'"';
		if ($db->getOneItem($q)) return false;

		return $db->insert('INSERT INTO s_userspycheck SET object_id = '.$_id.', user_id = '.$l['id_id'].', type_id = "'.$db->escape($_type).'"');
	}

	/*
		kollar om det finns en bevakning på aktuellt objekt, returnerar true/false
	*/
	function spyActive($_id, $_type)
	{
		global $db, $l;

		if (!is_numeric($_id)) return false;

		$q = 'SELECT COUNT(*) FROM s_userspycheck WHERE object_id='.$_id.' AND type_id="'.$db->escape($_type).'" AND user_id='.$l['id_id'];
		if ($db->getOneItem($q)) return true;
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
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT s.user_id FROM s_userspycheck s INNER JOIN s_user u ON u.id_id = s.user_id AND u.status_id = "1" WHERE s.type_id = "'.$db->escape($_type).'" AND s.object_id = '.$_id;
		$res = $db->getArray($q);

		$arr = str_replace(array('[object]', '[object_id]'), array($_object, $_id), gettxt('msg_spy_'.$_type));
		$arr = explode('[separator]', $arr);
		$title = trim($arr[0]);
		$msg = trim($arr[1]);
		$msg = str_replace('[object_url]', $arr[2], $msg);
		foreach($res as $row) {
			spyPostSend($row['user_id'], $title, $msg);
		}
		
		return true;
	}

	/*
		spyPostSend($_user, $_title, $_msg)
		skickar ut ett mail. ersätter user-class-funktionen spy().
	*/
	function spyPostSend($_user, $_title, $_msg)
	{
		global $db, $user;
		if (!is_numeric($_user)) return false;
		
		$q = 'INSERT INTO s_usermail SET '.
			'user_id = '.$_user.',sender_id = "0", status_id = "1",'.
			'sender_status = "2", user_read = "0", sent_cmt = "'.$db->escape($_msg).'",'.
			'sent_ttl = "'.$db->escape($_title).'",sent_date = NOW()';
		$db->insert($q);
		$user->counterIncrease('mail', $_user);
		$user->notifyIncrease('mail', $_user);
		return true;
	}
?>