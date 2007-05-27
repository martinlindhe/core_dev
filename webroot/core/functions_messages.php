<?
	define('MESSAGE_GROUP_INBOX',		1);
	define('MESSAGE_GROUP_OUTBOX',	2);

	function sendMessage($_id, $_subj, $_msg)
	{
		global $db, $session;
		if (!is_numeric($_id)) return false;

		//Adds message to recievers inbox
		$q = 'INSERT INTO tblMessages SET ownerId='.$_id.',fromId='.$session->id.',toId='.$_id.',subject="'.$db->escape($_subj).'",body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_INBOX;
		$db->insert($q);
		
		//Add message to senders outbox
		$q = 'INSERT INTO tblMessages SET ownerId='.$session->id.',fromId='.$session->id.',toId='.$_id.',subject="'.$db->escape($_subj).'",body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_OUTBOX;
		$db->insert($q);

		return true;
	}

	function getMessages($_group = 0)
	{
		global $db, $session;
		if (!is_numeric($_group)) return false;

		switch ($_group) {
			case MESSAGE_GROUP_INBOX:
				$q  = 'SELECT t1.*,t1.fromId AS otherId, t2.userName AS otherName ';
				$q .= 'FROM tblMessages AS t1 ';
				$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.fromId=t2.userId) ';
				$q .= 'WHERE t1.ownerId='.$session->id.' AND t1.groupId='.$_group.' ';
				$q .= 'ORDER BY timeCreated DESC';
				break;

			case MESSAGE_GROUP_OUTBOX:
				$q  = 'SELECT t1.*,t1.toId AS otherId, t2.userName AS otherName ';
				$q .= 'FROM tblMessages AS t1 ';
				$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.toId=t2.userId) ';
				$q .= 'WHERE t1.ownerId='.$session->id.' AND t1.groupId='.$_group.' ';
				$q .= 'ORDER BY timeCreated DESC';
				break;
				
			default:
				$q = 'SELECT * FROM tblMessages WHERE ownerId='.$session->id.' AND groupId='.$_group;
		}

		return $db->getArray($q);
	}

	function showMessages($_group = 0)
	{
		global $db, $session;
		if (!is_numeric($_group)) return false;
		
		if (!$_group && !empty($_GET['g']) && is_numeric($_GET['g'])) $_group = $_GET['g'];
		if (!$_group) $_group = MESSAGE_GROUP_INBOX;

		echo 'My messages<br/><br/>';

		echo ($_group==MESSAGE_GROUP_INBOX?'<b>INBOX</b>':'<a href="?g='.MESSAGE_GROUP_INBOX.'">INBOX</a>').'<br/>';
		echo ($_group==MESSAGE_GROUP_OUTBOX?'<b>OUTBOX</b>':'<a href="?g='.MESSAGE_GROUP_OUTBOX.'">OUTBOX</a>').'<br/>';
		echo '<br/>';

		$list = getMessages($_group);
		if (!$list) {
			echo 'No messages';
			return false;
		}
		
		echo '<div id="grid"></div>';

		echo '<script type="text/javascript">';
		echo 'var g = new OS3Grid();';
		echo 'g.set_headers("Subject", "Time", "Status");';
		
		$i=0;
		foreach ($list as $row) {
			echo 'g.add_row("'.($row['subject']?$row['subject']:'no subject').'","'.$row['timeCreated'].'","'.(!$row['timeRead']?'UNREAD':'READ').'");';
		}
		echo 'g.set_sortable(true);';
		echo 'g.set_highlight(true);';
		echo 'g.render("grid");';
		echo '</script>';
	}

?>