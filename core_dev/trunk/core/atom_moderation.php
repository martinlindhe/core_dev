<?php
/**
 * $Id$
 *
 * Functions for moderation
 *
 * Objectionable: Unallowed in public forums (discussion groups, guestbooks, diaries), configurable
 * Sensitive: Words that should trigger moderator notification upon certain posts, but is not blocked
 * Reserved usernames: Normal users shouldnt be able to create accounts with names such as admin, information, etc
 *
 * This module uses atom_comments.php to store comments to the moderation queue
 *
 * Requires tblModeration and tblStopwords
 *
 * Admin: this atom module has two admin pages: "moderation queue" and "edit stopwords"
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//STATUS: need rewrite: make the moderation queue an object

$config['moderation']['enabled'] = true;

define('MODERATION_FILE',						10);	//itemId tblFiles.fileId, a user reported a file
define('MODERATION_GUESTBOOK',					11);	//itemId = tblGuestbook.entryId
define('MODERATION_FORUM',						12);	//itemId = tblForum.itemId
define('MODERATION_BLOG',						13);
define('MODERATION_USER',						14);	//itemId = tblUser.userId
define('MODERATION_QUEUE',						15);	//used to put a user in a moderation queue
define('MODERATION_REPORTED_VIDEOPRESENTATION',	16);	//a user can report another user's video presentation. itemId = tblFiles.fileId of video pres
define('MODERATION_REPORTED_VIDEOMESSAGE',		17);	//a user can report a private video-message. itemId = tblFiles.fileId of video message
define('MODERATION_PRES_IMAGE',					18);	//a users presentation image

//Moderation queue type 1-49 is reserverd for core_dev use. Please use >= 50 for your own extensions

/* Stopwords */
define('STOPWORD_OBJECTIONABLE',		1);	//this type of words are forbidden to post
define('STOPWORD_SENSITIVE',			2);	//sensitive words, this type triggers auto-moderation in various modules
define('STOPWORD_RESERVED_USERNAME',	3);	//reserved usernames

/* lookup table for moderation types */
$lookup_moderation = array(
	MODERATION_FILE => 'File',
	MODERATION_GUESTBOOK => 'Guestbook',
	MODERATION_FORUM => 'Forum',
	MODERATION_BLOG => 'Blog',
	MODERATION_USER => 'User',
	MODERATION_QUEUE => 'Queue',
	MODERATION_REPORTED_VIDEOPRESENTATION => 'Videopresentation',
	MODERATION_REPORTED_VIDEOMESSAGE => 'Videomessage',
	MODERATION_PRES_IMAGE => 'Presentation Image'
	);

/**
 * Checks if the word(s) in $text are objectionable
 */
function isObjectionable($text)
{
	return checkStopword($text, STOPWORD_OBJECTIONABLE);
}

/**
 * Checks if the word(s) in  $text are sensitive
 */
function isSensitive($text)
{
	return checkStopword($text, STOPWORD_SENSITIVE);
}

/**
 * Checks if the $object is in the queue of $type
 */
function isInQueue($object, $type)
{
	global $db;

	if (!is_numeric($object) || !is_numeric($type)) return false;

	$q = 'SELECT queueId FROM tblModeration WHERE queueType = '.$type.' AND itemId = '.$object.' AND moderatedBy = 0 LIMIT 1';
	if ($db->getOneItem($q)) return true;
	return false;
}

/**
 * XXX
 */
function checkStopword($text, $_type)
{
	global $db;

	if (!is_numeric($_type)) return false;

	//Removes non-letters
	$text = str_replace('.', '', $text);
	$text = str_replace(',', '', $text);
	$text = str_replace('!', '', $text);
	$text = str_replace('?', '', $text);
	$text = str_replace('(', '', $text);
	$text = str_replace(')', '', $text);

	$text = normalizeString($text, array("\n", "\r"));

	$text = $db->escape($text);

	$list = explode(' ', $text);
	$list = array_unique($list);
	$list = array_values($list);

	$q = 'SELECT wordText,wordMatch FROM tblStopwords WHERE wordType='.$_type;
	$wordlist = $db->getArray($q);

	foreach ($list as $word) {
		foreach ($wordlist as $stopword) {
			if ($stopword['wordMatch'] == 1) {
				//Match stopword agains the whole input word
				if (strtolower($word) == strtolower($stopword['wordText'])) return true;
			} else {
				//Find stopword anywhere inside input word
				if (stristr($word, $stopword['wordText'])) return true;
			}
		}
	}

	return false;
}

/**
 * Checks if the word in $text is a reserved username
 * @todo integrate this with checkStopword() somehow
 */
function isReservedUsername($text)
{
	global $db;

	//Removes non-letters
	$text = str_replace('.', '', $text);
	$text = str_replace(',', '', $text);
	$text = str_replace('!', '', $text);
	$text = str_replace('?', '', $text);
	$text = str_replace('(', '', $text);
	$text = str_replace(')', '', $text);
	$text = $db->escape($text);

	$q = 'SELECT wordText,wordMatch FROM tblStopwords WHERE wordType='.STOPWORD_RESERVED_USERNAME;
	$list = $db->getArray($q);
	for ($i=0; $i<count($list); $i++) {
		if ($list[$i]["wordMatch"] == 1) {
			// Must match the whole word
			if (strtolower($text) == strtolower($list[$i]["wordText"])) return true;
		} else {
			// Word can be somewhere in the string
			if (stristr($text, $list[$i]["wordText"])) return true;
		}
	}

	return false;
}

/**
 * Returns all stopwords, optionally selected by $type
 */
function getStopwords($type = '')
{
	global $db;

	$q = 'SELECT * FROM tblStopwords';
	if (is_numeric($type)) $q .= ' WHERE wordType='.$type;
	$q .= ' ORDER BY wordText ASC';

	return $db->getArray($q);
}

/**
 * Adds a stopword of type $type if not already exists, return false on failure
 */
function addStopword($type, $word, $full)
{
	global $db;
	if (!is_numeric($type) || !is_numeric($full)) return false;

	$word = $db->escape($word);

	$q = 'SELECT wordId FROM tblStopwords WHERE wordText="'.$word.'" AND wordType='.$type;
	if ($db->getOneItem($q)) return false;

	$q = 'INSERT INTO tblStopwords SET wordText="'.$word.'",wordType='.$type.',wordMatch='.$full;
	return $db->insert($q);
}

/**
 * Removes a stopword
 */
function removeStopword($wordId)
{
	global $db;
	if (!is_numeric($wordId)) return false;

	$q = 'DELETE FROM tblStopwords WHERE wordId='.$wordId;
	$db->delete($q);
}

/**
 * Save changes to a stopword
 */
function setStopword($wordId, $wordText, $full)
{
	global $db;
	if (!is_numeric($wordId) || !is_numeric($full)) return false;

	$q = 'UPDATE tblStopwords SET wordText="'.$db->escape($wordText).'", wordMatch='.$full.' WHERE wordId='.$wordId;
	$db->update($q);
}

/**
 * Adds the forum item $itemId to the moderation queue tagged with reason $queueType
 * $triggeredBy is only used if $h->session is not available
 */
function addToModerationQueue($queueType, $itemId, $auto_triggered = false, $triggeredBy = 0)
{
	global $h, $db;
	if (!is_numeric($itemId) || !is_numeric($queueType) || !is_bool($auto_triggered) || !is_numeric($triggeredBy)) return false;

	if ($auto_triggered != '1') $auto_triggered = 0;

	$q = 'SELECT queueId FROM tblModeration WHERE itemId='.$itemId.' AND queueType='.$queueType.' AND autoTriggered='.$auto_triggered;
	$queueId = $db->getOneItem($q);
	if ($queueId) return $queueId;

	$q = 'INSERT INTO tblModeration SET queueType='.$queueType.',itemId='.$itemId.',creatorId='.($h->session ? $h->session->id : $triggeredBy).',autoTriggered='.$auto_triggered.',timeCreated=NOW()';
	return $db->insert($q);
}

/**
 * Get objects in moderation queue
 */
function getModerationQueue($_type, $_sql_limit = '', $_order = 'ASC')
{
	global $db;
	if (!is_numeric($_type) && !is_array($_type) && is_string($_order)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblModeration AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'WHERE t1.moderatedBy=0';
	if (is_numeric($_type) && $_type != 0) $q .= ' AND t1.queueType='.$_type;
	else if (is_array($_type) && !empty($_type)) {
		$q .= ' AND (';
		for ($i = 0; $i < count($_type); $i++) {
			$q .= 't1.queueType='.$_type[$i];
			if ($i < count($_type)-1) {
				$q .= ' OR ';
			}
		}
		$q .= ')';
	}
	$q .= ' ORDER BY t1.timeCreated '.$_order.$_sql_limit;
	return $db->getArray($q);
}

/**
 * Get objects in moderated queue
 */
function getModeratedQueue($_type, $_sql_limit = '', $_order = 'ASC')
{
	global $db;
	if (!is_numeric($_type) && !is_array($_type) && is_string($_order)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblModeration AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'WHERE t1.moderatedBy!=0';
	if (is_numeric($_type) && $_type != 0) $q .= ' AND t1.queueType='.$_type;
	else if (is_array($_type) && !empty($_type)) {
		$q .= ' AND (';
		for ($i = 0; $i < count($_type); $i++) {
			$q .= 't1.queueType='.$_type[$i];
			if ($i < count($_type)-1) {
				$q .= ' OR ';
			}
		}
		$q .= ')';
	}
	$q .= ' ORDER BY t1.timeCreated '.$_order.$_sql_limit;
	return $db->getArray($q);
}

/**
 * Get specified object in moderation queue
 */
function getModerationQueueItem($queueId)
{
	global $db;
	if (!is_numeric($queueId)) return false;

	$q = 'SELECT * FROM tblModeration WHERE queueId='.$queueId;
	return $db->getOneRow($q);
}

/**
 * Get number of objects in moderation queue
 */
function getModerationQueueCount($_type = 0)
{
	global $db;
	if (!is_array($_type) && !is_numeric($_type)) return false;

	$q = 'SELECT COUNT(queueId) FROM tblModeration WHERE moderatedBy=0';
	if (is_numeric($_type) && $_type != 0) $q .= ' AND queueType='.$_type;
	else if (is_array($_type) && !empty($_type)) {
		$q .= ' AND (';
		for ($i = 0; $i < count($_type); $i++) {
			$q .= 'queueType='.$_type[$i];
			if ($i < count($_type)-1) {
				$q .= ' OR ';
			}
		}
		$q .= ')';
	}

	return $db->getOneItem($q);
}

/**
 * Get number of objects in moderated queue
 */
function getModeratedQueueCount($_type = 0)
{
	global $db;
	if (!is_array($_type) && !is_numeric($_type)) return false;

	$q = 'SELECT COUNT(queueId) FROM tblModeration WHERE moderatedBy!=0';
	if (is_numeric($_type) && $_type != 0) $q .= ' AND queueType='.$_type;
	else if (is_array($_type) && !empty($_type)) {
		$q .= ' AND (';
		for ($i = 0; $i < count($_type); $i++) {
			$q .= 'queueType='.$_type[$i];
			if ($i < count($_type)-1) {
				$q .= ' OR ';
			}
		}
		$q .= ')';
	}

	return $db->getOneItem($q);
}

/**
 * Removes the specified queue-id from the moderation queue
 */
function removeFromModerationQueue($queueId)
{
	global $h, $db;
	if (!$h->session->isWebmaster || !is_numeric($queueId)) return false;

	$q = 'UPDATE tblModeration SET moderatedBy='.$h->session->id.',timeModerated=NOW() WHERE queueId='.$queueId;
	$db->update($q);
}

/**
 * Really deletes from moderation queue, used when deleting forum threads
 */
function removeFromModerationQueueByType($_type, $itemId)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($itemId)) return false;

	$q = 'DELETE FROM tblModeration WHERE  queueType='.$_type.' AND itemId='.$itemId;
	$db->delete($q);
}

/**
 * Helper function for a "report object" feature
 */
function reportDialog($_type, $_id)
{
	if (!empty($_POST['report_reason']) || !empty($_POST['report_text'])) {

		$queueId = addToModerationQueue($_type, $_id);
		addComment(COMMENT_MODERATION, $queueId, $_POST['report_reason'].': '.$_POST['report_text']);

		echo t('Thank you. Your report has been recieved.');
		return;
	}
	switch ($_type) {
		case MODERATION_USER:
			echo '<h2>'.t('Report user').'</h2>';
			echo t('Please choose the reason as to why you wish to report this user').':<br/>';
			break;

		case MODERATION_FILE:
			echo '<h2>'.t('Report file').'</h2>';
			echo t('Please choose the reason as to why you wish to report this file').':<br/>';
			break;

		default: die('reportDialog() unhandled type: '.$_type);
	}

	echo '<form method="post" action="">';
	echo t('Reason').': ';
	echo '<select name="report_reason">';
	echo '<option value=""></option>';
	echo '<option value="Harassment">'.t('Harassment').'</option>';
	echo '<option value="Other">'.t('Other').'</option>';
	echo '</select><br/>';

	echo t('Please describe your reason for the abuse report').':<br/>';
	echo '<textarea name="report_text" rows="6" cols="40"></textarea><br/>';

	echo '<input type="submit" class="button" value="'.t('Send report').'"/>';
	echo '</form>';
}
?>
