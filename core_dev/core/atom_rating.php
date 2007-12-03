<?
/**
 * atom_rating.php - implements general rating functionality, used by various modules
 *
 * Currently used by the following modules: News
 *
 * \author Martin Lindhe, 2006-2007
 */

	define('RATE_NEWS',		1);
	define('RATE_BLOG',		2);
	define('RATE_IMAGE',	3);	//todo: implement

	/**
	 * Adds a rating + keeps track if the user has already added a rating
	 *
	 * \param $_type type of item
	 * \param $_id id of item to rate
	 * \param $_rating is a integer between 1-5, or 1-100 (or what the min & max-values are)
	 * \return nothing
	 */
	function rateItem($_type, $_id, $_rating)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($_type) || !is_numeric($_id) || !is_numeric($_rating)) return false;

		//1. check if user already voted
		$q = 'SELECT rateId FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id.' AND userId='.$session->id;
		if ($db->getOneItem($q)) return false;

		//2. save the vote
		$q = 'INSERT INTO tblRatings SET type='.$_type.',itemId='.$_id.',rating='.$_rating.',userId='.$session->id.',timeRated=NOW()';
		$db->insert($q);

		//3. count current average of the rating
		$q = 'SELECT AVG(rating) FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id;
		$avgrating = $db->getOneItem($q);

		$q = 'SELECT COUNT(rateId) FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id;
		$ratingcnt = $db->getOneItem($q);

		switch ($_type) {
			case RATE_BLOG:
				//4. update average
				$q = 'UPDATE tblBlogs SET rating='.$avgrating.',ratingCnt='.$ratingcnt.' WHERE blogId='.$_id;
				$db->query($q);
				break;

			case RATE_NEWS:
				//4. update average
				$q = 'UPDATE tblNews SET rating='.$avgrating.',ratingCnt='.$ratingcnt.' WHERE newsId='.$_id;
				$db->query($q);
				break;

			default: die('rateItem unknown type');
		}
	}

	/**
	 * Is specified item rated?
	 *
	 * \param $_type type of item
	 * \param $_id id of item to rate
	 * \return true if current user already voted for specified object
	 */
	function isRated($_type, $_id)
	{
		global $db, $session;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		$q = 'SELECT rateId FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id.' AND userId='.$session->id;
		if ($db->getOneItem($q)) return true;
		return false;
	}


	/**
	 * Returns average rating for specified item
	 *
	 * \param $_type type of item
	 * \param $_id id of item to rate
	 * \return average rating for item
	 */
	function getRating($_type, $_id)
	{
		global $db;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		switch ($_type) {
			case RATE_BLOG:
				$q = 'SELECT rating,ratingCnt FROM tblBlogs WHERE blogId='.$_id;
				break;

			case RATE_NEWS:
				$q = 'SELECT rating,ratingCnt FROM tblNews WHERE newsId='.$_id;
				break;

			default:
				die('getRating dies');
		}

		return $db->getOneRow($q);
	}

	/**
	 * Generates a general "rate this"-gadget used by various modules
	 *
	 * \param $_type type of item
	 * \param $_id id of item to rate
	 * \return html block for self-contained rating gadget
	 */
	function ratingGadget($_type, $_id)
	{
		global $db, $session;

		if (!is_numeric($_type) || !is_numeric($_id)) return false;

		if (!$session->id || isRated($_type, $_id)) return showRating($_type, $_id);

		if (!empty($_POST['rate_gadget'])) {
			rateItem($_type, $_id, $_POST['rate_gadget']);
			return showRating($_type, $_id);
		}

		$result  = 'Rate this:<br/>';
		$result .= '<form method="post" action="">';
		$result .= '<select name="rate_gadget">';
		$result .= '<option value="">&nbsp;</option>';
		for ($i=1; $i<=5; $i++) {
			$result .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$result .= '</select>';
		$result .= ' <input type="submit" class="button" value="Rate"/>';
		$result .= '</form>';

		return $result;
	}

	/**
	 * Shows current votes
	 *
	 * \param $_type type of item
	 * \param $_id id of item to rate
	 * \return html block for self-contained "current votes" gadget
	 */
	function showRating($_type, $_id)
	{
		global $config;

		$result  = 'Current rating<br/><br/>';
		$row = getRating($_type, $_id);
		for ($i=1; $i<=5; $i++) {
			if ($i <= $row['rating']) {
				$result .= '<img src="'.$config['core_web_root'].'gfx/icon_star_full.png" alt="star"/>';
			} else {
				$result .= '<img src="'.$config['core_web_root'].'gfx/icon_star_empty.png" alt="star"/>';
			}
		}
		$result .= '<br/><br/>';
		$result .= $row['rating'].' / 5 in '.$row['ratingCnt'].($row['ratingCnt']==1?' vote':' votes');

		return $result;
	}

?>