<?php
/**
 * $Id$
 *
 * @author Linus Wiklund, 2008 <linus.wiklund@ljw.se>
 */

/**
 * Function that prints the html for a pop-up 1on1 chat
 * NOTE: Dont use any other code that uses createXHTMLHeader
 * on the page that uses this function.
 *
 * @param $otherId user ID of the user the logged in user wants to chat with
 */
function chat_1on1_XHTML($otherId)
{
	global $session, $body_onload, $db;

	$userId = $session->id;

	$otherName = Users::getName($otherId);
	if (empty($otherName)) {
		return false;
	}
	$myName = Users::getName($userId);

	$oldMsgs = array_reverse($db->getArray('SELECT * FROM tblChat WHERE ((userId = '.$userId.' AND authorId = '.$otherId.') OR (userId = '.$otherId.' AND authorId = '.$userId.'))  AND msgRead = 1 ORDER BY msgDate desc LIMIT 10'));

	$body_onload[] = 'chat_onload('.$otherId.', '.$userId.", '".$otherName."', '".$myName."')";
	createXHTMLHeader();

	echo '<div align="center">';
	echo '<h2>Chat</h2><br/>';

	echo '<br/>';

	echo '<div id="chat_div" align="left">';
	foreach ($oldMsgs as $msg) {
		echo '['.$msg['msgDate'].'] &lt;'.Users::getName($msg['authorId']).'&gt; '.$msg['msg'].'<br/>';
		$db->update('UPDATE tblChat SET msgRead = 1 WHERE userId = '.$userId.' AND authorId = '.$otherId.' AND msgDate = "'.$msg['msgDate'].'" AND msgRead = 0 LIMIT 1');

	}
	echo '</div>';

	echo '<a name="form"></a>';

	echo '<form name="chat_form" onSubmit="chat_send(document.chat_form.chat_message);return false;">';

	echo '<input type="text" name="chat_message"/>';
	echo '<input type="button" name="send" value="Send" onClick="chat_send(document.chat_form.chat_message)"/>';

	echo '</form>';

	echo '</div>';

	echo '</body></html>';
}

/**
 * Get the number of new chat messages during the specified time period
 */
function getChatMessagesCountPeriod($dateStart, $dateStop)
{
	global $db;

	$q = 'SELECT count(userId) AS cnt FROM tblChat WHERE msgDate BETWEEN "'.$db->escape($dateStart).'" AND "'.$db->escape($dateStop).'"';
	return $db->getOneItem($q);
}


/**
 * Get number of all chat messages
 */
function getAllChatMessagesCount()
{
	global $db;

	$q = 'SELECT count(userId) AS cnt FROM tblChat';
	return $db->getOneItem($q);
}

/**
 * Get all chat messages
 */
function getAllChatMessages($_limit_sql)
{
	global $db;

	$q = 'SELECT * FROM tblChat';
	if ($_limit_sql) $q .= $_limit_sql;
	return $db->getArray($q);
}


?>
