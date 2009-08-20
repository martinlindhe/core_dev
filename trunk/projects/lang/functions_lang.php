<?php
/**
 * $Id$
 *
 * Natural language understanding and self-learning routines
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Adds a new word to database
 *
 * @param $langId id of language to associate word with
 * @param $word the word to add
 * @param $pron optional pronounciation rules
 * @return false on failure
 */
function addWord($langId, $word, $pron = '')
{
	global $db;
	if (!is_numeric($langId)) return false;

	$word = trim($word);
	$pron = trim($pron);
	if (!$word) return false;

	$word = $db->escape($word);
	$pron = $db->escape($pron);

	$q = 'SELECT id FROM tblWords WHERE word="'.$word.'" AND lang='.$langId;
	$check = $db->getOneItem($q);
	if ($check) return false;

	$q = 'INSERT INTO tblWords SET word="'.$word.'",pron="'.$pron.'",lang='.$langId;
	return $db->insert($q);
}

function deleteWord($_id)
{
	global $db;
	if (!is_numeric($_id)) return false;

	$q = 'DELETE FROM tblWords WHERE id='.$_id;
	return $db->delete($q);
}

/**
 * Returns details of one word
 *
 * @param $wordId id of word to get details for
 * @return array of data
 */
function getWord($wordId)
{
	global $db;
	if (!is_numeric($wordId)) return false;

	$q = 'SELECT * FROM tblWords WHERE id='.$wordId;
	return $db->getOneRow($q);
}

/**
 * Returns word id for one word
 *
 * @param $langId id of language of word
 * @param $word the word to get id of
 * @return id of word
 */
function getWordId($langId, $word)
{
	global $db;
	if (!is_numeric($langId)) return false;

	$q = 'SELECT id FROM tblWords WHERE word="'.$db->escape($word).'" AND lang='.$langId;
	return $db->getOneItem($q);
}

/**
 * Returns all words for this language
 *
 * @param $langId language to get words for
 * @return array of words belonging to $langId
 */
function getWords($langId)
{
	global $db;
	if (!is_numeric($langId)) return false;

	$q = 'SELECT id,word FROM tblWords WHERE lang='.$langId;
	$q .= ' ORDER BY word ASC';
	return $db->getArray($q);
}

/**
 * Returns number of words for this language
 *
 * @param $langId language to get words for
 * @return number
 */
function getWordCount($langId)
{
	global $db;
	if (!is_numeric($langId)) return false;

	$q = 'SELECT COUNT(*) FROM tblWords WHERE lang='.$langId;
	return $db->getOneItem($q);
}

/**
 * Returns all entries that match this word
 *
 * @param $word word to search for
 * @return array of matches
 */
function getWordMatches($word)
{
	global $db;

	$word = trim($word);
	if (!$word) return false;

	$q = 'SELECT * FROM tblWords WHERE word="'.$db->escape($word).'"';
	return $db->getArray($q);
}

/**
 * Cleans up input text for easier processing.
 *
 * @param $text the text to parse
 * @return cleaned up version of $text
 */
function parseText($text)
{
	$text = trim($text);

	//Clean up the text
	$text = str_replace("\t", ' ', $text);		//Turn tab into space
	$text = str_replace("\n", ' ', $text);		//Newline into space
	$text = str_replace("\r", ' ', $text);		//Linefeed into space

	$text = str_replace(',', ' , ', $text);		//Makes sure commas are threated separatley
	$text = str_replace('(', ' ( ', $text);		//
	$text = str_replace(')', ' ) ', $text);		//
	$text = str_replace(':', ' : ', $text);		//
	$text = str_replace('"', ' " ', $text);		//
	$text = str_replace('*', ' * ', $text);		//

	$text = str_replace('  ', ' ', $text);		//Reduce space clutter

	//Split up the text in array of sentences separated by .
	return explode('.', $text);
}

/**
 * Returns true if input is a number or a special character
 */
function notWord($word)
{
	if (is_numeric($word)) return true;
	switch ($word) {
		case '(':case ')':
		case '{':case '}':
		case '[':case ']':
		case '#':
		case '+':case '-':
		case '*':case '/':
		case '%':case '&':
		case '\\':
		case '.':case ',':
		case ':':case ';':
		case '\'':
		case '`':
		case '"':
		case '½':case '¼':
		case '€':case '$':case '£':
		case '@':
		case '':
			return true;
	}

	return false;
}

/**
 * Returns true if we need human assistance to decide
 */
function unsureWord($word)
{
	if (strpos($word, '(') !== false) return true;
	if (strpos($word, ')') !== false) return true;
	if (strpos($word, ':') !== false) return true;
	if (strpos($word, ';') !== false) return true;
	if (strpos($word, '[') !== false) return true;
	if (strpos($word, ']') !== false) return true;
	if (strpos($word, '"') !== false) return true;
	if (strpos($word, '$') !== false) return true;

	if (is_numeric(substr($word, 0, 1))) return true;
	if (is_numeric(substr($word, 1, 1))) return true;
	if (strlen($word) == 1) return true;

	return false;
}

/**
 * Takes a body of text and saves as many observations as possible about the text
 *
 * @param $langId id of the language of the text
 * @param $text the text to analyze
 * @return nothing
 */
function analyzeText($langId, $text)
{
	if (!is_numeric($langId) || !$text) return false;

	$sentences = parseText($text);

	for ($i=0; $i<count($sentences); $i++) {
		//Split up the sentences in array of words separated by space
		$words = explode(' ', $sentences[$i]);

		for ($j=0; $j<count($words); $j++) {
			if (notWord($words[$j])) {
				echo '<div class="critical">Skipped invalid word <b>'.$words[$j].'</b></div>';
				continue;
			}
			if (unsureWord($words[$j])) {
				//FIXME: checkbox & submit to add the rest of the words
				echo '<div style="background-color:#FD9534">Not sure if this is a word, skipping <b></b>'.$words[$j].'</b></div>';
				continue;
			}

			$wordId = addWord($langId, $words[$j]);
			if ($wordId) {
				echo '<div class="okay"><b>'.$words[$j].'</b> added to database</div>';
			} else {
				$wordId = getWordId($langId, $words[$j]);
			}

			if ($wordId) {
				//save observations
				if ($j == 0) {
					//word can occur in the beginning of sentence
					learnWordRelation(WORDRELATION_STARTSENTENCE, $wordId);
				}
				if ($j == count($words)) {
					//word can occur at end of sentences
					echo 'END OF SENTENCE<br/>';
					learnWordRelation(WORDRELATION_ENDSENTENCE, $wordId);
				}

				if (count($words) == 1) {
					//word can be full sentence by itself
					echo 'FULL SENTENCE WORD<br/>';
					learnWordRelation(WORDRELATION_ALONEINSENTENCE, $wordId);
				}

				if ($j > 0) {
					//word can occur after $words[$j - 1]
					$otherId = getWordId($langId, $words[$j - 1]);
					learnWordRelation(WORDRELATION_COMESAFTER, $wordId, $otherId);
				}

				if ($j < (count($words)-1)) {
					//word can occur before $words[$j + 1]
					$otherId = getWordId($langId, $words[$j + 1]);
					learnWordRelation(WORDRELATION_COMESBEFORE, $wordId, $otherId);
				}

			}

		}
	}
}

define('WORDRELATION_STARTSENTENCE',		1);	//word can be in the beginning of a sentence
define('WORDRELATION_ENDSENTENCE',			2);	//word can end a sentence
define('WORDRELATION_ALONEINSENTENCE',	3);	//word can form a whole sentence alone
define('WORDRELATION_COMESAFTER',				4);	//word can come after otherWordId
define('WORDRELATION_COMESBEFORE',			5);	//word can come before otherWordId

/**
 * Learns the relation of a word through observation.
 *
 * @param $type type of relation
 * @param $wordId id of word we learn relation for
 * @param $otherWordId optional second word relation, depending on type
 */
function learnWordRelation($type, $wordId, $otherWordId = 0)
{
	global $db;
	if (!is_numeric($type) || !is_numeric($wordId) || !is_numeric($otherWordId)) return false;

	$q = 'SELECT COUNT(*) FROM tblWordRelations WHERE relationType='.$type.' AND wordId='.$wordId.' AND otherId='.$otherWordId;
	if ($db->getOneItem($q)) return false;

	$q = 'INSERT INTO tblWordRelations SET relationType='.$type.',wordId='.$wordId.',otherId='.$otherWordId;
	$db->insert($q);
}

/**
 * Tries to determine what language the input text is written in
 *
 * @param $text text to analyze
 * @return array of info about the guess
 */
function guessLanguage($text)
{
	if (!$text) return false;

	$sentences = parseText($text);

	$result = array();
	$tot = 0;

	for ($i=0; $i<count($sentences); $i++) {
		//Split up the sentences in array of words separated by space
		$words = explode(' ', $sentences[$i]);

		for ($j=0; $j<count($words); $j++) {
			//For each word, check what language it maches in the database

			$list = getWordMatches($words[$j]);
			for ($k=0; $k<count($list); $k++) {
				if (!isset($result[ $list[$k]['lang'] ])) $result[ $list[$k]['lang'] ] = 0;
				$result[ $list[$k]['lang'] ]++;
				$tot++;
			}
		}
	}

	foreach ($result as $lang => $cnt) {
		if (!$lang) echo 'Unknown';
		else echo getCategoryName(CATEGORY_LANGUAGE, $lang);
		echo ': '.round(($cnt/$tot)*100, 2).'%<br/>';
	}

	return $result;
}

function generateAcronyms($_lang, $_acronym, $_ammount)
{
	global $db;
	if (!is_numeric($_lang) || !is_numeric($_ammount)) return false;

	$out = array();
	for ($i = 0; $i < $_ammount; $i++) {
		$curr = '';
		for ($j=0; $j<strlen($_acronym); $j++) {
			$c = substr($_acronym, $j, 1);
			$q = 'SELECT word FROM tblWords WHERE lang='.$_lang.' AND word LIKE "'.$c.'%" ORDER BY RAND() LIMIT 1';
			$curr .= $db->getOneItem($q).' ';
		}
		$out[] = $curr;
	}
	return $out;
}

?>
