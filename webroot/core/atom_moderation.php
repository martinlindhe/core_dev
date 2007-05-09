<?
	/* atom_moderation.php - Funktioner för moderering

		Objectionable: Unallowed in public forums (discussion groups, guestbooks, diaries), configurable
		Sensitive: Words that should trigger moderator notification upon certain posts, but is not blocked
		Reserved usernames: Normal users shouldnt be able to create accounts with names such as admin, information, etc

		this module uses atom_comments.php to store comments to the moderation queue

	*/

	/* Moderation queue entry types */
	define('MODERATION_REPORTED_POST',				1);
	define('MODERATION_OBJECTIONABLE_POST',		2);
	define('MODERATION_SENSITIVE_POST',				3);
	define('MODERATION_REPORTED_USER',				10);
	define('MODERATION_SENSITIVE_GUESTBOOK',	11);
	define('MODERATION_REPORTED_PHOTO',				12);
	define('MODERATION_REPORTED_BLOG',				13);
	define('MODERATION_SENSITIVE_BLOG',				14);


	/* Stopwords */
	define('STOPWORD_OBJECTIONABLE',				1);
	define('STOPWORD_SENSITIVE',						2);
	define('STOPWORD_RESERVED',							3);



	/* kontrollerar om ordet/orden i $text är stötante */
	function isObjectionable($text)
	{
		global $db;

		/* Removes non-letters */
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$text = str_replace(".", "", $text);
		$text = str_replace(",", "", $text);
		$text = str_replace("!", "", $text);
		$text = str_replace("?", "", $text);
		$text = str_replace("(", "", $text);
		$text = str_replace(")", "", $text);

		while (1) {
			$newtext = str_replace('  ', ' ', $text);
			if ($newtext != $text) {
				$text = $newtext;
			} else {
				break;
			}
		}
		$text = $db->escape(trim($text));

		$list = explode(' ', $text);
		$list = array_unique($list);
		$list = array_values($list);

		//fixme: foreach loopar
		for ($i=0; $i<count($list); $i++) {
			
			$q = 'SELECT wordText,wordMatch FROM tblStopwords WHERE wordType='.STOPWORD_OBJECTIONABLE;
			$wordlist = $db->getArray($q);

			for ($j=0; $j<count($wordlist); $j++) {
				if ($wordlist[$j]['wordMatch'] == 1) {
					/* Måste matcha hela ordet */
					if (strtolower($list[$i]) == strtolower($wordlist[$j]['wordText'])) {
						return true;
					}
				} else {
					/* Räcker med att ordet finns med nånstan */
					if (stristr($list[$i], $wordlist[$j]['wordText'])) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/* kontrollerar om ordet/orden i $text är känsligt */
	function isSensitive($text)
	{
		global $db;

		/* Removes non-letters */
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$text = str_replace(".", "", $text);
		$text = str_replace(",", "", $text);
		$text = str_replace("!", "", $text);
		$text = str_replace("?", "", $text);
		$text = str_replace("(", "", $text);
		$text = str_replace(")", "", $text);

		while(1) {
			$newtext = str_replace('  ', ' ', $text);
			if ($newtext != $text) {
				$text = $newtext;
			} else {
				break;
			}
		}
		$text = $db->escape(trim($text));

		$list = explode(' ', $text);
		$list = array_unique($list);
		$list = array_values($list);

		//fixme: foreach loopar
		for ($i=0; $i<count($list); $i++) {
			
			$sql = 'SELECT wordText,wordMatch FROM tblStopwords WHERE wordType='.STOPWORD_SENSITIVE;
			$wordlist = dbArray($db, $sql);

			for ($j=0; $j<count($wordlist); $j++) {
				if ($wordlist[$j]['wordMatch'] == 1) {
					/* Måste matcha hela ordet */
					if (strtolower($list[$i]) == strtolower($wordlist[$j]['wordText'])) {
						return true;
					}
				} else {
					/* Räcker med att ordet finns med nånstan */
					if (stristr($list[$i], $wordlist[$j]['wordText'])) {
						return true;
					}
				}
			}
		}
		return false;
	}

	function isReservedUsername(&$db, $text)
	{
		/* kontrollerar om ordet i $text är ett reserverat användarnamn */

		/* Removes non-letters */
		$text = str_replace(".", "", $text);
		$text = str_replace(",", "", $text);
		$text = str_replace("!", "", $text);
		$text = str_replace("?", "", $text);
		$text = str_replace("(", "", $text);
		$text = str_replace(")", "", $text);

		$text = dbAddSlashes($db, $text);

		$sql = 'SELECT wordText,wordMatch FROM tblStopwords WHERE wordType='.STOPWORD_RESERVED;
		$list = dbArray($db, $sql);
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
	function getStopwords(&$db, $type = '')
	{
		if (is_numeric($type)) {
			$sql = 'SELECT * FROM tblStopwords WHERE wordType='.$type.' ORDER BY wordText ASC';
		} else {
			$sql = 'SELECT * FROM tblStopwords ORDER BY wordText ASC';
		}

		return dbArray($db, $sql);
	}


	/* Adds a stopword of type $type if not already exists, else returns false */
	function addStopword(&$db, $word, $type, $full)
	{
		if (!is_numeric($type) || !is_numeric($full)) return false;
		$word = dbAddSlashes($db, $word);

		$check = dbQuery($db, 'SELECT wordId FROM tblStopwords WHERE wordText="'.$word.'" AND wordType='.$type );
		if (!dbNumRows($check)) {
			dbQuery($db, 'INSERT INTO tblStopwords SET wordText="'.$word.'",wordType='.$type.',wordMatch='.$full);
			return true;
		}
		return false;
	}

	function removeStopword(&$db, $wordId)
	{
		if (!is_numeric($wordId)) return false;

		$sql = 'DELETE FROM tblStopwords WHERE wordId='.$wordId;
		dbQuery($db, $sql);
	}

	function setStopword(&$db, $wordId, $wordText, $full)
	{
		if (!is_numeric($wordId) || !is_numeric($full)) return false;
		$wordText = dbAddSlashes($db, $wordText);

		$sql = 'UPDATE tblStopwords SET wordText="'.$wordText.'", wordMatch='.$full.' WHERE wordId='.$wordId;
		dbQuery($db, $sql);
	}

	/* Adds the forum item $itemId to the moderation queue tagged with reason $queueType */
	function addToModerationQueue($itemId, $queueType)
	{
		global $db;

		if (!is_numeric($itemId) || !is_numeric($queueType)) return false;

		$q = 'SELECT queueId FROM tblModerationQueue WHERE itemId='.$itemId.' AND queueType='.$queueType;
		$queueId = $db->getOneItem($q);
		if ($queueId) return $queueId;

		$q = 'INSERT INTO tblModerationQueue SET queueType='.$queueType.',itemId='.$itemId.',timestamp='.time();
		$db->query($q);
		return $db->insert_id;
	}

	function getModerationQueue(&$db)
	{
		$sql = 'SELECT * FROM tblModerationQueue ORDER BY timestamp ASC';
		return dbArray($db, $sql);
	}
	
	function getModerationQueueItem(&$db, $queueId)
	{
		if (!is_numeric($queueId)) return false;

		$sql = 'SELECT * FROM tblModerationQueue WHERE queueId='.$queueId;
		return dbOneResult($db, $sql);
	}

	function getModerationQueueCount(&$db)
	{
		$sql = 'SELECT COUNT(queueId) FROM tblModerationQueue';
		return dbOneResultItem($db, $sql);
	}

	/* Removes the specified queue-id from the moderation queue */
	function removeFromModerationQueue(&$db, $queueId)
	{
		if (!is_numeric($queueId)) return false;

		$sql = 'DELETE FROM tblModerationQueue WHERE queueId='.$queueId;
		dbQuery($db, $sql);
	}

	/* Removes both moderation queue and comments entry */
	function removeFromModerationQueueByItemId(&$db, $itemId, $queueType)
	{
		if (!is_numeric($itemId) || !is_numeric($queueType)) return false;

		$sql = 'SELECT queueId FROM tblModerationQueue WHERE itemId='.$itemId.' AND queueType='.$queueType;
		$queueId = dbOneResultItem($db, $sql);
		if (!$queueId) return false;

		removeFromModerationQueue($db, $queueId);
		deleteComments($db, COMMENT_MODERATION_QUEUE, $queueId);
	}
?>