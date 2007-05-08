<?
	//lang stuff
	
	function addWord(&$db, $langId, $word, $pron)
	{
		if (!is_numeric($langId)) return false;
		
		$word = trim($word);
		$pron = trim($pron);
		if (!$word) return false;
		
		$word = dbAddSlashes($db, $word);
		$pron = dbAddSlashes($db, $pron);
		
		$sql = 'SELECT id FROM tblWords WHERE word="'.$word.'" AND lang='.$langId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return false;

		$sql = 'INSERT INTO tblWords SET word="'.$word.'",pron="'.$pron.'",lang='.$langId;
		dbQuery($db, $sql);

		return $db['insert_id'];
	}

	function getWord(&$db, $wordId)
	{
		if (!is_numeric($wordId)) return false;
		
		$sql = 'SELECT * FROM tblWords WHERE id='.$wordId;
		return dbOneResult($db, $sql);
	}
	
	/* Returns all words for this language */
	function getWords(&$db, $langId)
	{
		if (!is_numeric($langId)) return false;
		
		$sql = 'SELECT id,word FROM tblWords WHERE lang='.$langId;
		
		return dbArray($db, $sql);
	}
	
	/* Returns all entries that match this word */
	function getWordMatches(&$db, $word)
	{
		$word = trim($word);
		if (!$word) return false;

		$word = dbAddSlashes($db, $word);
		
		$sql = 'SELECT * FROM tblWords WHERE word="'.$word.'"';
		return dbArray($db, $sql);
	}
	
	function analyzeText(&$db, $langId, $text)
	{
		$text = trim($text);
		if (!is_numeric($langId) || !$text) return false;
		
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
		$sentences = explode('.', $text);

		for ($i=0; $i<count($sentences); $i++) {
			//Split up the sentences in array of words separated by space
			$words = explode(' ', $sentences[$i]);

			for ($j=0; $j<count($words); $j++) {
				//För varje ord, kolla om det finns i databasen, annars spara

				$check = addWord($db, $langId, $words[$j], '');
				if ($check) {
					echo '<b>'.$words[$j].'</b> added to database<br>';
				} else {
					//echo '<b>'.$words[$j].'</b> skipped, was already in database<br>';
				}
			}
		}
	}


	//todo: all of parsing is same to analyzeText(), lets share functions
	function guessLanguage(&$db, $text)
	{
		$text = trim($text);
		if (!$text) return false;
		
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
		$sentences = explode('.', $text);
		
		$result = array();

		for ($i=0; $i<count($sentences); $i++) {
			//Split up the sentences in array of words separated by space
			$words = explode(' ', $sentences[$i]);

			for ($j=0; $j<count($words); $j++) {
				//För varje ord, kolla vilka språk det matchar med i databasen
				
				$list = getWordMatches($db, $words[$j]);
				for ($k=0; $k<count($list); $k++) {
					//echo $list[$k]['lang'].'<br>';
					$result[$words[$j]][$k] = $list[$k]['lang'];
				}
			}
		}

		print_r($result);


	}


?>