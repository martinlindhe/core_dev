<?
	/* atom_moderation.php - Funktioner för moderering

		Objectionable: Unallowed in public forums (discussion groups, guestbooks, diaries), configurable
		Sensitive: Words that should trigger moderator notification upon certain posts, but is not blocked
		Reserved usernames: Normal users shouldnt be able to create accounts with names such as admin, information, etc

		this module uses atom_comments.php to store comments to the moderation queue

		requires tblModeration and tblStopwords

		Admin: this atom module has two admin pages: "moderation queue" and "edit stopwords"
	*/

	$config['moderation']['enabled'] = true;

	/* Moderation queue entry types */
	//define('MODERATION_REPORTED_POST',				1);
	//define('MODERATION_OBJECTIONABLE_POST',		2);
	//define('MODERATION_SENSITIVE_POST',				3);
	//define('MODERATION_REPORTED_USER',				10);
	//define('MODERATION_REPORTED_PHOTO',				12);

	define('MODERATION_GUESTBOOK',	11);	//itemId = tblGuestbook.entryId
	define('MODERATION_FORUM',			12);	//itemId = tblForum.itemId
	define('MODERATION_BLOG',				13);
	define('MODERATION_USER',				14);	//itemId = tblUser.userId


	/* Stopwords */
	define('STOPWORD_OBJECTIONABLE',			1);	//this type of words are forbidden to post
	define('STOPWORD_SENSITIVE',					2);	//sensitive words, this type triggers auto-moderation in various modules
	define('STOPWORD_RESERVED_USERNAME',	3);	//reserved usernames


	/* kontrollerar om ordet/orden i $text är stötante */
	function isObjectionable($text)
	{
		return checkStopword($text, STOPWORD_OBJECTIONABLE);
	}

	/* kontrollerar om ordet/orden i $text är känsligt */
	function isSensitive($text)
	{
		return checkStopword($text, STOPWORD_SENSITIVE);
	}

	function checkStopword($text, $_type)
	{
		global $db;

		if (!is_numeric($_type)) return false;

		/* Removes non-letters */
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("\r", ' ', $text);
		$text = str_replace('.', '', $text);
		$text = str_replace(',', '', $text);
		$text = str_replace('!', '', $text);
		$text = str_replace('?', '', $text);
		$text = str_replace('(', '', $text);
		$text = str_replace(')', '', $text);

		while (1) {
			$newtext = str_replace('  ', ' ', $text);
			if ($newtext == $text) break;
			$text = $newtext;
		}
		$text = $db->escape(trim($text));

		$list = explode(' ', $text);
		$list = array_unique($list);
		$list = array_values($list);

		$q = 'SELECT wordText,wordMatch FROM tblStopwords WHERE wordType='.$_type;
		$wordlist = $db->getArray($q);

		foreach ($list as $word) {
			foreach ($wordlist as $stopword) {
				if ($stopword['wordMatch'] == 1) {
					/* Match stopword agains the whole input word */
					if (strtolower($word) == strtolower($stopword['wordText'])) return true;
				} else {
					/* Find stopword anywhere inside input word */
					if (stristr($word, $stopword['wordText'])) return true;
				}
			}
		}

		return false;
	}

	/* kontrollerar om ordet i $text är ett reserverat användarnamn */
	//todo: integrate this with checkStopword() somehow
	function isReservedUsername($text)
	{
		global $db;

		/* Removes non-letters */
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
				/* Måste matcha hela ordet */
				if (strtolower($text) == strtolower($list[$i]["wordText"])) {
					return true;
				}
			} else {
				/* Räcker med att ordet finns med nånstan */
				if (stristr($text, $list[$i]["wordText"])) {
					return true;
				}
			}
		}

		return false;
	}


	/* Returnerar alla $type words eller alla */
	function getStopwords($type = '')
	{
		global $db;

		if (is_numeric($type)) {
			$q = 'SELECT * FROM tblStopwords WHERE wordType='.$type.' ORDER BY wordText ASC';
		} else {
			$q = 'SELECT * FROM tblStopwords ORDER BY wordText ASC';
		}

		return $db->getArray($q);
	}


	/* Adds a stopword of type $type if not already exists, return false on failure */
	function addStopword($type, $word, $full)
	{
		global $db;

		if (!is_numeric($type) || !is_numeric($full)) return false;
		$word = $db->escape($word);

		$id = $db->getOneItem('SELECT wordId FROM tblStopwords WHERE wordText="'.$word.'" AND wordType='.$type);
		if ($id) return false;

		return $db->insert('INSERT INTO tblStopwords SET wordText="'.$word.'",wordType='.$type.',wordMatch='.$full);
	}

	function removeStopword($wordId)
	{
		global $db;

		if (!is_numeric($wordId)) return false;

		$q = 'DELETE FROM tblStopwords WHERE wordId='.$wordId;
		$db->query($q);
	}

	function setStopword($wordId, $wordText, $full)
	{
		global $db;

		if (!is_numeric($wordId) || !is_numeric($full)) return false;

		$q = 'UPDATE tblStopwords SET wordText="'.$db->escape($wordText).'", wordMatch='.$full.' WHERE wordId='.$wordId;
		$db->query($q);
	}

	/* Adds the forum item $itemId to the moderation queue tagged with reason $queueType */
	function addToModerationQueue($queueType, $itemId, $auto_triggered = false)
	{
		global $db, $session;

		if (!is_numeric($itemId) || !is_numeric($queueType) || !is_bool($auto_triggered)) return false;
		if ($auto_triggered != '1') $auto_triggered = 0;

		$q = 'SELECT queueId FROM tblModeration WHERE itemId='.$itemId.' AND queueType='.$queueType.' AND autoTriggered='.$auto_triggered;
		$queueId = $db->getOneItem($q);
		if ($queueId) return $queueId;

		$q = 'INSERT INTO tblModeration SET queueType='.$queueType.',itemId='.$itemId.',creatorId='.$session->id.',autoTriggered='.$auto_triggered.',timeCreated=NOW()';

		return $db->insert($q);
	}

	function getModerationQueue($_sql_limit = '')
	{
		global $db;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblModeration AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE t1.moderatedBy=0 ORDER BY t1.timeCreated ASC'.$_sql_limit;

		return $db->getArray($q);
	}

	function getModerationQueueItem($queueId)
	{
		global $db;

		if (!is_numeric($queueId)) return false;

		$q = 'SELECT * FROM tblModeration WHERE queueId='.$queueId;
		return $db->getOneRow($q);
	}

	function getModerationQueueCount()
	{
		global $db;

		$q = 'SELECT COUNT(queueId) FROM tblModeration WHERE moderatedBy=0';
		return $db->getOneItem($q);
	}

	/* Removes the specified queue-id from the moderation queue */
	function removeFromModerationQueue($queueId)
	{
		global $db, $session;

		if (!$session->isAdmin || !is_numeric($queueId)) return false;

		$q = 'UPDATE tblModeration SET moderatedBy='.$session->id.',timeModerated=NOW() WHERE queueId='.$queueId;
		$db->query($q);
	}
	
	//really deletes from moderation queue, used when deleting forum threads
	function removeFromModerationQueueByType($_type, $itemId)
	{
		global $db;
		if (!is_numeric($_type) || !is_numeric($itemId)) return false;
		
		$q = 'DELETE FROM tblModeration WHERE  queueType='.$_type.' AND itemId='.$itemId;
		$db->delete($q);
	}

	function reportUserDialog($_id)
	{
		if (!empty($_POST['report_reason']) || !empty($_POST['report_text'])) {

			$queueId = addToModerationQueue(MODERATION_USER, $_id);
			addComment(COMMENT_MODERATION, $queueId, $_POST['report_reason'].': '.$_POST['report_text']);

			echo 'Thanks. Your report has been recieved.';
			return;
		}

		echo '<h1>Abuse</h1>';
		echo 'If you want to block this user. Click here - fixme<br/><br/>';

		echo '<h2>Report user form</h2>';
		echo 'Please choose the reason as to why you wish to report this user:<br/>';
		echo '<form method="post" action="">';
		echo 'Reason: ';
		echo '<select name="report_reason">';
		echo '<option value=""></option>';
		echo '<option value="Harassment">Harassment</option>';
		echo '<option value="Other">Other</option>';
		echo '</select><br/>';

		echo 'Please describe your reason for the abuse report.<br/>';
		echo '<textarea name="report_text" rows="6" cols="40"></textarea><br/>';

		echo '<input type="submit" class="button" value="Send report"/>';
		echo '</form>';
	}
?>