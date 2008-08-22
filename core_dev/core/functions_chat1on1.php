<?php
/**
 * $Id: functions_email.php 3743 2008-05-22 12:35:11Z ml $
 *
 * \author Linus Wiklund, 2008 <linus.wiklund@ljw.se>
 */

/**
 * Function that prints the html for a pop-up 1on1 chat
 * NOTE: Dont use any other code that uses createXHTMLHeader
 * on the page that uses this function.
 *
 * \param $otherId user ID of the user the logged in user wants to chat with
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

?>
