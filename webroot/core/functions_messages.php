<?
	define('MESSAGE_GROUP_INBOX',		0);
	define('MESSAGE_GROUP_OUTBOX',	1);

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

	function showMessages($_group)
	{
		global $db, $session;
		if (!is_numeric($_group)) return false;
		
		echo 'My messages - ';
		switch ($_group) {
			case MESSAGE_GROUP_INBOX:
				echo 'INBOX<br/>';
				break;

			case MESSAGE_GROUP_OUTBOX:
				echo 'OUTBOX<br/>';
				break;
		}

		$list = getMessages($_group);
		if (!$list) {
			echo 'No messages';
			return false;
		}
		
		echo '<div id="grid"></div>';

		echo '<script type="text/javascript">';
		echo 'Grid.addcell("Subject",170);';
		echo 'Grid.addcell("Time",230);';
		echo 'Grid.addcell("Read",110);';
		echo 'Grid.Draw();';

		foreach ($list as $row) {
			echo "Grid.addrow([['".($row['subject']?$row['subject']:'<i>no subject</i>')."','".$row['timeCreated']."','".(!$row['timeRead']?'UNREAD':'READ')."']]);\n";
		}
		echo '</script>';
	}

?>