<?
	/*
		atom_rating.php - set of functions to implement user rating of various objects, used by other modules

		Written by Martin Lindhe, 2006-2007

		todo: ajax-gadget där man kan rösta på ett objekt
	*/

	define('RATE_NEWS',		1);
	define('RATE_BLOG',		2);
	define('RATE_IMAGE',	3);

	/* Lägg ett omdöme + håll reda på att användaren lagt sitt omdöme
		$_rating är ett heltal mellan 1 till 100 (eller 0 till 99) ?
	*/
	function rateItem($_type, $_id, $_rating)
	{
		if (!is_numeric($_type) || !is_numeric($_id) || !is_numeric($_rating)) return false;

		//1. kolla om användaren redan röstat
		$q = 'SELECT * FROM tblRatingData WHERE userId='.$session->id;
		if (1) return false;

		//2. spara röstningen
		$q = 'INSERT INTO tblRatingData SET type='.$_type.',itemId='.$_id.',rating='.$_rating.',userId='.$session->id.',timeRated=NOW()';
		$db->query($q);
		
		//3. räkna ut aktuella medelvärdet av omdömet
		$q = 'SELECT * FROM tblRatingData WHERE type='.$_type.',itemId='.$_id;
		$avgrating = $db->getOneItem($q);
		
		switch ($_type) {
			case RATE_NEWS:
				//4. uppdatera medelvärdet
				$q = 'UPDATE tblNews SET rating='.$avgrating.' WHERE newsId='.$_id;
				break;
				
			default: die('rateItem unknown type');
		}
	}

	/* Returnerar omdömet för detta objekt */
	function getRatring($_type, $_id)
	{
	}

?>