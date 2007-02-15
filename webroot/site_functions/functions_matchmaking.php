<?
	/*
		functions_matchmaking.php - Funktioner för matchmaking
	*/

	/* Creates a new question, and returns its ID */
	function addMatchmakingQuestion(&$db, $parentId, $text)
	{
		if (!is_numeric($parentId)) return false;
		$text = dbAddSlashes($text);

		$query = dbQuery($db, 'INSERT INTO tblMatchmaking SET parentId='.$parentId.',itemText="'.$text.'"');
		return $db['insert_id'];
	}

	/* Returns a 2d array with all questions and all their alternatives */
	function getMatchmakingQuestions(&$db)
	{
		$list = dbArray($db, 'SELECT * FROM tblMatchmaking WHERE parentId=0 ORDER BY itemId ASC');
		for ($i=0; $i<count($list); $i++) {
			$list[$i]['alt'] = dbArray($db, 'SELECT * FROM tblMatchmaking WHERE parentId='.$list[$i]['itemId'].' ORDER BY itemId ASC');
		}

		return $list;
	}

	/* Removes all items with id $itemId, and all items with that as their parentId */
	/* Also all user answers related to this specific question */
	function removeMatchmakingQuestion(&$db, $itemId)
	{
		if (!is_numeric($itemId)) return false;

		dbQuery($db, 'DELETE FROM tblMatchmakingAnswers WHERE itemId='.$itemId.' OR answerId='.$itemId );
		dbQuery($db, 'DELETE FROM tblMatchmaking WHERE itemId='.$itemId.' OR parentId='.$itemId );
	}

	/* Returns the number of questions in db */
	function getMatchmakingCnt(&$db)
	{
		$sql = 'SELECT COUNT(itemId) FROM tblMatchmaking WHERE parentId=0';
		return dbOneResultItem($db, $sql);
	}

	/* Returns all unanswered matchmaking questions for $userId */
	function getUnansweredMatchmakingQuestions(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT t1.itemId AS itemId,t1.itemText AS itemText ';
		$sql .= 'FROM tblMatchmaking AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblMatchmakingAnswers AS t2 ON ';
		$sql .= '(t1.itemId = t2.itemId AND t2.userId='.$userId.') ';
		$sql .= 'WHERE t1.parentId=0 ';
		$sql .= 'AND userId IS NULL';
		$list = dbArray($db, $sql);

		for ($i=0; $i<count($list); $i++) {
			$list[$i]['alt'] = dbArray($db, 'SELECT * FROM tblMatchmaking WHERE parentId='.$list[$i]['itemId'].' ORDER BY itemText ASC');
		}

		return $list;
	}

	function getAnsweredMatchmakingQuestions(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT t1.itemId, t1.itemText, t2.answerId, t3.itemText AS answerText ';
		$sql .= 'FROM tblMatchmaking AS t1 ';
		$sql .= 'INNER JOIN tblMatchmakingAnswers AS t2 ON ';
		$sql .= '(t1.itemId = t2.itemId AND t2.userId='.$userId.') ';
		$sql .= 'INNER JOIN tblMatchmaking AS t3 ON (t2.answerId=t3.itemId) ';
		$sql .= 'WHERE t1.parentId=0';
		return dbArray($db, $sql);
	}
	
	/* Returnerar true om användaren har obesvarade matchmakingquestions */
	function hasUnansweredMatchmakingQuestions(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT COUNT(t1.itemId) FROM tblMatchmaking AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblMatchmakingAnswers AS t2 ON (t1.itemId = t2.itemId AND t2.userId='.$userId.') ';
		$sql .= 'WHERE t1.parentId=0 AND t2.userId IS NULL';
		$num = dbOneResultItem($db, $sql);

		if ($num > 0) return true;
		return false;
	}

	/* Registrerar en användares svar */
	function addMatchmakingAnswer(&$db, $userId, $itemId, $answerId)
	{
		if (!is_numeric($userId) || !is_numeric($itemId) || !is_numeric($answerId)) return false;

		dbQuery($db, 'INSERT INTO tblMatchmakingAnswers SET itemId='.$itemId.',answerId='.$answerId.',userId='.$userId );
	}

	/* Compares the answers from two users and returns a value representing the ammount of similarity in percent */
	function matchmakeCompare(&$db, $user1, $user2)
	{
		if (!is_numeric($user1) || !is_numeric($user2)) return false;

		$check = dbQuery($db, 'SELECT itemId FROM tblMatchmakingAnswers WHERE userId='.$user1 );
		if (!dbNumRows($check)) return false;

		$sql  = 'SELECT COUNT(t1.itemId) ';
		$sql .= 'FROM tblMatchmakingAnswers AS t1 ';
		$sql .= 'INNER JOIN tblMatchmakingAnswers AS t2 ON (t2.userId='.$user2.' AND t1.answerId=t2.answerId) ';
		$sql .= 'WHERE t1.userId='.$user1;

		$cnt = dbOneResultItem($db, $sql);

		$x = ($cnt / getMatchmakingCnt($db))*100;
		$x = round($x, 2);
		return $x;
	}


	/* Returns the $limit best matches for $userId in a array */
	//userId e för användaren att matcha mot, excludeId e för att exkludera ytterligare en användare från matchningen, t.ex användaren som vill se resultatet
	function getBestMatchmakes(&$db, $userId, $count, $excludeId = 0)
	{
		if (!is_numeric($userId) || !is_numeric($count) || !is_numeric($excludeId)) return false;

		/* Väljer samtliga användare som gjort testet (dvs svarat på alla frågor), förrutom $excludeId */
		$sql  = 'SELECT t2.userId,t3.userName,t2.itemId,t2.answerId,t4.answerId AS myAnswer FROM tblMatchmaking AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblMatchmakingAnswers AS t2 ON (t1.itemId=t2.itemId) ';
		$sql .= 'INNER JOIN tblUsers AS t3 ON (t2.userId=t3.userId) ';
		$sql .= 'LEFT OUTER JOIN tblMatchmakingAnswers AS t4 ON (t2.itemId=t4.itemId AND t4.userId='.$userId.') ';
		$sql .= 'WHERE t1.parentId=0 AND t2.userId!='.$excludeId.' AND t2.userId!='.$userId.' AND t2.userId IS NOT NULL ';
		$sql .= 'ORDER BY t2.userId ASC';
		
		$all = dbArray($db, $sql);		//TODO: DEN FUNKAR INTE KORREKT, RETURNERAR ANVÄNDARE SOM INTE SVARAT PÅ ALLA FRÅGORNA!!!, så jag har löst det i php istället men det är FULT
		
		$j = 0;
		for ($i=0; $i<count($all); $i++) {
			if (isset($list[$j]['userId']) && ($all[$i]['userId'] != $list[$j]['userId'])) {
				$j++;
			}
			$list[$j]['userId'] = $all[$i]['userId'];
			$list[$j]['userName'] = $all[$i]['userName'];
			if (!isset($list[$j]['answers'])) $list[$j]['answers']=0;
			$list[$j]['answers']++;
			if (!isset($list[$j]['score'])) $list[$j]['score']=0;
			
			if ($all[$i]['answerId'] == $all[$i]['myAnswer']) {
				$list[$j]['score']++;
			}
		}
		
		if (isset($list)) {
			/* Konverterar till procent */
			$max = getMatchmakingCnt($db);		
			for ($i=0; $i<count($list); $i++) {
				$list[$i]['score'] = ($list[$i]['score'] / $max)*100;
				$list[$i]['score'] = round($list[$i]['score'], 2);
			}

			$list = aSortBySecondIndex($list, 'score');
			$list = array_reverse($list);
		
			//Tar bort alla som inte svarat på samtliga frågor
			$j = 0;
			for ($i=0; $i<count($list); $i++) {
				if ($list[$i]['answers'] == $max) {
					$result[$j++] = $list[$i];
				}
			}
				
			//Only return the X best matches
			for ($i=0; $i<$j; $i++) {
				if ($j > $count) break;
				$result[$i]=$result[$i];
			}

			return $result;
		}
		return;
	}

	function matchmakeSearch(&$db, $values)
	{
		/* Väljer samtliga användare som gjort testet (dvs svarat på alla frågor) */
		$sql  = 'SELECT t2.userId,t3.userName,t2.itemId,t2.answerId FROM tblMatchmaking AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblMatchmakingAnswers AS t2 ON (t1.itemId=t2.itemId) ';
		$sql .= 'INNER JOIN tblUsers AS t3 ON (t2.userId=t3.userId) ';
		$sql .= 'WHERE t1.parentId=0 AND t2.userId IS NOT NULL ';
		$sql .= 'ORDER BY t2.userId ASC';
		
		$all = dbArray($db, $sql);		//TODO: DEN FUNKAR INTE KORREKT, RETURNERAR ANVÄNDARE SOM INTE SVARAT PÅ ALLA FRÅGORNA!!!, så jag har löst det i php istället men det är FULT

		
		$j = 0;
		for ($i=0; $i<count($all); $i++) {
			if (isset($list[$j]['userId']) && ($all[$i]['userId'] != $list[$j]['userId'])) {
				$j++;
			}
			$list[$j]['userId'] = $all[$i]['userId'];
			$list[$j]['userName'] = $all[$i]['userName'];
			if (!isset($list[$j]['answers'])) $list[$j]['answers']=0;
			$list[$j]['answers']++;
			if (!isset($list[$j]['score'])) $list[$j]['score']=0;
			
			if ($all[$i]['answerId'] == $values[$all[$i]['itemId']] ) {
				$list[$j]['score']++;
			}
		}
		
		if (isset($list)) {
			$max = getMatchmakingCnt($db);		

			//Tar bort alla som inte svarat rätt på samtliga frågor
			$j = 0;
			for ($i=0; $i<count($list); $i++) {
				if (($list[$i]['answers'] == $max) && ($list[$i]['score'] == $max)) {
					$result[$j++] = $list[$i];
				}
			}
		}
		if (isset($result)) return $result;
		return;
	}

	function displayBestMatchmakes(&$db, $userId, $otherId, $limit=5)
	{
		$str = '<table width="100%" cellpadding=0 cellspacing=0 border=0>';

		if (!hasUnansweredMatchmakingQuestions($db, $userId)) {
			$list = getBestMatchmakes($db, $userId, $limit, $otherId);
			if ($list) {
				if ($_SESSION['loggedIn']) {
					if ($otherId != $userId) {
						/* Jämför dig med den här användaren */
						$mymatch = matchmakeCompare($db, $otherId, $userId);
						if ($mymatch === false) {
							$str .= '<tr><td colspan=2><a href="matchmaking.php">Gör testet &raquo;</a></td></tr>';
						} else {
							$str .= '<tr>';
							$str .= '<td width=50>'.showMatchmakeResult($mymatch).'</td>';
							$str .= '<td>&nbsp;'.$_SESSION['userName'].'</td>';
							$str .= '</tr>';
						}
						$str .= '<tr><td colspan=2>&nbsp;</td></tr>';
					}
				}
				/* Visa de bästa matcharna för den här användaren */
				for ($i=0; $i<count($list); $i++) {
					$str .= '<tr>';
					$str .= '<td width=50>'.showMatchmakeResult($list[$i]['score']).'</td>';
					$str .= '<td>&nbsp;'.nameLink($list[$i]['userId'], $list[$i]['userName']).'</td>';
					$str .= '</tr>';
				}
			} else {
				$str .= '<tr><td>';
					$str .= 'Det finns inga anv&auml;ndare att j&auml;mf&ouml;ra med';
				$str .= '</td></tr>';
			}
		} else {
			$str .= '<tr><td>';
			if ($otherId == $userId) {
				$str .= '<a href="matchmaking.php">G&ouml;r testet &raquo;</a>';
			} else {
				$str .= 'Den h&auml;r anv&auml;ndaren har inte gjort testet.';
			}
			$str .= '</td></tr>';
		}
		$str .= '</table>';
		
		return $str;
	}
?>