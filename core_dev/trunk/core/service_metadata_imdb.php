<?php
/**
 * $Id$
 *
 * Helper functions dealing with imdb.com
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

/**
 * @param $id imdb id
 * @return true if $id is a imdb ib
 */
function is_imdb_id($id)
{
	if (strpos($id, ' ')) return false;
	$pattern = "((tt|ch|nm|co)([0-9]){7})";

	if (preg_match($pattern, $id))
		return true;

	return false;
}
