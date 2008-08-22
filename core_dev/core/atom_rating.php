<?php
/**
 * $Id$
 *
 * Implements general rating functionality, used by various modules
 *
 * Currently used by the following modules: News
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('RATE_NEWS',		1);
define('RATE_BLOG',		2);
define('RATE_FILE',		3);

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

	//Check if user already voted
	$q = 'SELECT rateId FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id.' AND userId='.$session->id;
	if ($db->getOneItem($q)) return false;

	//Save the vote
	$q = 'INSERT INTO tblRatings SET type='.$_type.',itemId='.$_id.',rating='.$_rating.',userId='.$session->id.',timeRated=NOW()';
	$db->insert($q);

	//Count current average of the rating
	$q = 'SELECT AVG(rating) FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id;
	$avgrating = $db->getOneItem($q);

	$q = 'SELECT COUNT(rateId) FROM tblRatings WHERE type='.$_type.' AND itemId='.$_id;
	$ratingcnt = $db->getOneItem($q);

	//Update average
	switch ($_type) {
		case RATE_BLOG:
			$q = 'UPDATE tblBlogs SET rating='.$avgrating.',ratingCnt='.$ratingcnt.' WHERE blogId='.$_id;
			$db->update($q);
			break;

		case RATE_NEWS:
			$q = 'UPDATE tblNews SET rating='.$avgrating.',ratingCnt='.$ratingcnt.' WHERE newsId='.$_id;
			$db->update($q);
			break;

		case RATE_FILE:
			$q = 'UPDATE tblFiles SET rating='.$avgrating.',ratingCnt='.$ratingcnt.' WHERE fileId='.$_id;
			$db->update($q);
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

		case RATE_FILE:
			$q = 'SELECT rating,ratingCnt FROM tblFiles WHERE fileId='.$_id;
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
	global $db, $session, $config;
	if (!is_numeric($_type) || !is_numeric($_id)) return false;

	if (!$session->id || isRated($_type, $_id) ||
		($_type == RATE_FILE && Files::getOwner($_id) == $session->id))
		return showRating($_type, $_id);

	$result = t('Rate this').':<br/>';

	$row = getRating($_type, $_id);
	$curr = $row['rating'];

	$result .= '<div id="rate_file">';
	$result .= '<div id="star">';
	$result .= '<ul id="star'.$_id.'" class="star" onmousedown="star.update(event,this,'.$_type.','.$_id.')" onmousemove="star.cur(event,this)" title="'.t('Rate this').'">';
	$result .= '<li id="starCur'.$_id.'" class="curr" title="'.$curr.'%" style="width: '.($curr).'px;"></li>';	//80 = 67px.. ?
	$result .= '</ul>';
	$result .= '<div id="starUser'.$_id.'" class="user">'.$curr.'%</div>';
	$result .= '<br style="clear: both;">';
	$result .= '</div>';
	$result .= '</div>';

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

	$row = getRating($_type, $_id);

	$result = t('Current rating').'<br/><br/>';
	//$result .= $row['rating'];
	//$result .= '<br/><br/>';
	//FIXME draw cute stars instead
	if ($row['ratingCnt']) {
		$result .= $row['rating'].'% '.t('in').' '.$row['ratingCnt'].' '.($row['ratingCnt']==1?t('vote'):t('votes'));
	} else {
		$result .= t('Not rated yet.');
	}

	return $result;
}
?>
