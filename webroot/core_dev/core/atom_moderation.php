<?
	/* atom_moderation.php - Funktioner f�r moderering

		Objectionable: Unallowed in public forums (discussion groups, guestbooks, diaries), configurable
		Sensitive: Words that should trigger moderator notification upon certain posts, but is not blocked
		Reserved usernames: Normal users shouldnt be able to create accounts with names such as admin, information, etc

		this module uses atom_comments.php to store comments to the moderation queue

		requires tblModerationQueue and tblStopwords

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
	define('MODERATION_BLOG',				13);


	/* Stopwords */
	define('STOPWORD_OBJECTIONABLE',			1);	//this type of words are forbidden to post
	define('STOPWORD_SENSITIVE',					2);	//sensitive words, this type triggers auto-moderation in various modules
	define('STOPWORD_RESERVED_USERNAME',	3);	//reserved usernames


	/* kontrollerar om ordet/orden i $text �r st�tante */
	function isObjectionable($text)
	{
		return checkStopword($text, STOPWORD_OBJECTIONABLE);
	}

	/* kontrollerar om ordet/orden i $text �r k�nsligt */
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

	/* kontrollerar om ordet i $text �r ett reserverat anv�ndarnamn */
	//todo: integrera denna med checkStopword somehow
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
				/* M�ste matcha hela ordet */
				if (strtolower($text) == strtolower($list[$i]["wordText"])) {
					return true;
				}
			} else {
				/* R�cker med att ordet finns med n�nstan */
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

		$q = 'SELECT queueId FROM tblModerationQueue WHERE itemId='.$itemId.' AND queueType='.$queueType.' AND autoTriggered='.$auto_triggered;
		$queueId = $db->getOneItem($q);
		if ($queueId) return $queueId;

		$q = 'INSERT INTO tblModerationQueue SET queueType='.$queueType.',itemId='.$itemId.',creatorId='.$session->id.',autoTriggered='.$auto_triggered.',timeCreated=NOW()';

		return $db->insert($q);
	}

	function getModerationQueue()
	{
		global $db;

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblModerationQueue AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$q .= 'WHERE t1.moderatedBy=0 ORDER BY t1.timeCreated ASC';

		return $db->getArray($q);
	}

	function getModerationQueueItem($queueId)
	{
		global $db;

		if (!is_numeric($queueId)) return false;

		$q = 'SELECT * FROM tblModerationQueue WHERE queueId='.$queueId;
		return $db->getOneRow($q);
	}

	function getModerationQueueCount()
	{
		global $db;

		$q = 'SELECT COUNT(queueId) FROM tblModerationQueue';
		return $db->getOneItem($q);
	}

	/* Removes the specified queue-id from the moderation queue */
	function removeFromModerationQueue($queueId)
	{
		global $db, $session;

		if (!$session->isAdmin || !is_numeric($queueId)) return false;

		$q = 'UPDATE tblModerationQueue SET moderatedBy='.$session->id.',timeModerated=NOW() WHERE queueId='.$queueId;
		$db->query($q);
	}

?>