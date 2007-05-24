<?
	define('MESSAGE_GROUP_INBOX',		0);
	define('MESSAGE_GROUP_OUTBOX',	1);

	function sendMessage($_id, $_msg)
	{
		global $db, $session;
		if (!is_numeric($_id)) return false;

		//Adds message to recievers inbox
		$q = 'INSERT INTO tblMessages SET ownerId='.$_id.',fromId='.$session->id.',toId='.$_id.',body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_INBOX;
		$db->insert($q);
		
		//Add message to senders outbox
		$q = 'INSERT INTO tblMessages SET ownerId='.$session->id.',fromId='.$session->id.',toId='.$_id.',body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_OUTBOX;
		$db->insert($q);

		return true;
	}

	function getMessages($_group = 0)
	{
		global $db, $session;
		if (!is_numeric($_group)) return false;

		$q = 'SELECT * FROM tblMessages WHERE ownerId='.$session->id.' AND groupId='.$_group;
		return $db->getArray($q);
	}

?>