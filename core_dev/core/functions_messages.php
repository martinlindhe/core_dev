<?
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_locale.php');	//for translations

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

	function systemMessage($_id, $_subj, $_msg)
	{
		global $db, $session;
		if (!is_numeric($_id)) return false;

		//Adds message to recievers inbox
		$q = 'INSERT INTO tblMessages SET ownerId='.$_id.',fromId=0,toId='.$_id.',subject="'.$db->escape($_subj).'",body="'.$db->escape($_msg).'",timeCreated=NOW(),groupId='.MESSAGE_GROUP_INBOX;
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

	function getMessage($_id)
	{
		global $db, $session;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblMessages WHERE ownerId='.$session->id.' AND msgId='.$_id;
		$row = $db->getOneRow($q);

		return $row;
	}

	function markMessageAsRead($_id)
	{
		global $db, $session;
		if (!is_numeric($_id)) return false;
		
		//Marks the message as read if its recipent & owner is current user.
		$q = 'UPDATE tblMessages SET timeRead=NOW() WHERE ownerId='.$session->id.' AND toId='.$session->id.' AND msgId='.$_id;
		return $db->update($q);
	}

	function showMessages($_group = 0)
	{
		global $db, $session;
		if (!is_numeric($_group)) return false;
		
		if (!empty($_GET['read']) && is_numeric($_GET['read'])) {
			//Shows one message
			$msg = getMessage($_GET['read']);
			if (!$msg) return false;

			echo '<div class="msg">';
			echo '<div class="msg_head">';
				echo ($msg['subject'] ? $msg['subject']:t('No subject')).' '.t('at').' '.$msg['timeCreated'].'<br/>';
				if ($msg['fromId']) {
					echo t('From').' '.Users::link($msg['fromId']).' <a href="messages.php?id='.$msg['fromId'].'">Svara</a><br/>';
				} else {
					echo '<b>'.t('System message').'</b><br/>';
				}
				echo t('To').' '.Users::link($msg['toId']).'<br/>';
				echo (!$msg['timeRead']?'UNREAD':'READ');
			echo '</div>';
			echo '<div class="msg_body">';
				echo nl2br($msg['body']);
			echo '</div>';
			echo '</div>';
			
			markMessageAsRead($_GET['read']);
			
			return true;
		}
		
		if (!$_group && !empty($_GET['g']) && is_numeric($_GET['g'])) $_group = $_GET['g'];
		if (!$_group) $_group = MESSAGE_GROUP_INBOX;

		echo ($_group==MESSAGE_GROUP_INBOX?'<b>'.t('INBOX').'</b>':'<a href="?g='.MESSAGE_GROUP_INBOX.'">'.t('INBOX').'</a>').'<br/>';
		echo ($_group==MESSAGE_GROUP_OUTBOX?'<b>'.t('OUTBOX').'</b>':'<a href="?g='.MESSAGE_GROUP_OUTBOX.'">'.t('OUTBOX').'</a>').'<br/>';
		echo '<br/>';

		$list = getMessages($_group);
		if (!$list) {
			echo t('No messages');
			return false;
		}

		$i=0;
		foreach ($list as $row) {
			echo '<a href= "'.$_SERVER['PHP_SELF'].'?read='.$row['msgId'].'">';
			echo ($row['subject']?$row['subject']:'no subject').', '.$row['timeCreated'].', '.(!$row['timeRead']?t('UNREAD'):t('READ')).'<br/>';
			echo '</a>';
		}

		return true;
	}
?>
