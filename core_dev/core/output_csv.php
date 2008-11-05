<?php
/**
 * $Id$
 *
 * CSV output helper functions
 *
 * http://en.wikipedia.org/wiki/Comma-separated_values
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * Escapes data if nessecary
 */
function csvEscape($str)
{
	//Fields with embedded double-quote characters must be delimited with double-quote characters,
	// and the embedded double-quote characters must be represented by a pair of double-quote characters.
	if (strpos($str, '"') !== false) {
		return '"'.str_replace('"', '""', $str).'"';
	}

	//Fields with embedded commas or line breaks must be delimited with double-quote characters.
	//Fields with leading or trailing spaces must be delimited by double-quote characters.
	if (
		strpos($str, $this->separator) !== false ||
		strpos($str, "\r") !== false || strpos($str, "\n") !== false ||
		substr($str, 0, 1) == ' ' || substr($str, -1) == ' ' ||
		substr($str, 0, 1) == "\t" || substr($str, -1) == "\t")
	{
		return '"'.$str.'"';
	}
}

?>
