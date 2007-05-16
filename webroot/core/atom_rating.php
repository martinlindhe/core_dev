<?
	/* atom_rating.php - implements general rating functionality, used by various modules

		Currently used by the following modules: News

		By Martin Lindhe, 2007
	*/

	define('RATE_NEWS',		1);
	define('RATE_BLOG',		2);
	define('RATE_IMAGE',	3);

	/* Lägg ett omdöme + håll reda på att användaren lagt sitt omdöme
		$_rating är ett heltal mellan 1 till 5, eller 1 till 100 (eller vad nu min & max-värdena är)
	*/
	function rateItem($_type, $_id, $_rating)	//todo: rename to rateObject()
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type) || !is_numeric($_id) || !is_numeric($_rating)) return false;

		//1. kolla om användaren redan röstat
		$q = 'SELECT rateId FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id.' AND userId='.$session->id;
		if ($db->getOneItem($q)) return false;

		//2. spara röstningen
		$q = 'INSERT INTO tblRatings SET type='.$_type.',itemId='.$_id.',rating='.$_rating.',userId='.$session->id.',timeRated=NOW()';
		$db->query($q);

		//3. räkna ut aktuella medelvärdet av omdömet
		$q = 'SELECT AVG(rating) FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id;
		$avgrating = $db->getOneItem($q);

		$q = 'SELECT COUNT(rateId) FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id;
		$ratingcnt = $db->getOneItem($q);

		switch ($_type) {
			case RATE_NEWS:
				//4. uppdatera medelvärdet
				$q = 'UPDATE tblNews SET rating='.$avgrating.',ratingCnt='.$ratingcnt.' WHERE newsId='.$_id;
				$db->query($q);
				break;

			default: die('rateItem unknown type');
		}
	}

	/* Returns true if current user already voted for specified object */
	function isRated($_type, $_id)
	{
		global $db, $session;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'SELECT rateId FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id.' AND userId='.$session->id;
		if ($db->getOneItem($q)) return true;
		return false;
	}


	/* Returnerar omdömet för detta objekt */
	function getRating($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'SELECT rating,ratingCnt FROM tblNews WHERE newsId='.$_id;
		return $db->getOneRow($q);
	}

	/* Generates a general "rate this"-gadget used by various modules */
	function ratingGadget($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;
		
		$not_rated = !isRated(RATE_NEWS, $_id);

		if ($not_rated && !empty($_POST['rate_gadget'])) {
			rateItem($_type, $_id, $_POST['rate_gadget']);
			$not_rated = false;
		}

		if ($not_rated) {
			$result  = 'Rate this news article:<br/>';
			$result .= '<form method="post" action="">';
			$result .= '<select name="rate_gadget">';
			$result .= '<option value="">&nbsp;</option>';
			for ($i=1; $i<=5; $i++) {
				$result .= '<option value="'.$i.'">'.$i.'</option>';
			}
			$result .= '</select>';
			$result .= ' <input type="submit" class="button" value="Rate"/>';
			$result .= '</form>';
		} else {
			//Show current votes
			$result  = 'Current rating<br/><br/>';
			$row = getRating($_type, $_id);
			for ($i=1; $i<=5; $i++) {
				if ($i <= $row['rating']) {
					$result .= '<img src="/gfx/icon_star_full.png" alt="star"/>';
				} else {
					$result .= '<img src="/gfx/icon_star_empty.png" alt="star"/>';
				}
			}
			$result .= '<br/><br/>';
			$result .= $row['rating'].' / 5 in '.$row['ratingCnt'].' votes<br/>';
		}

		return $result;
	}

?>