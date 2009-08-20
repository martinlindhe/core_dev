<?php
/**
 * $Id$
 *
 * Utility functions to deal with GUID numbers.
 *
 * A Globally Unique Identifier (or GUID) is a 128-bit (16 byte) number.
 *
 * http://en.wikipedia.org/wiki/GUID
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

/**
 * Works as built in strrev() but on character pairs.
 * input string must have even length
 *
 * FIXME: replace usage with some built-in PHP function
 */
function strrev2($str)
{
	if (strlen($str) % 2) return false;

	$ret = '';
	for ($i = strlen($str); $i >= 0; $i -= 2) {
		$ret .= substr($str, $i, 2);
	}
	return $ret;
}

/**
 * Converts a GUID-formatted string to a hex value
 * @param $guid GUID in the format "3F2504E0-4F89-11D3-9A0C-0305E82C3301"
 * $return GUID in the format E004253F894FD3119A0C0305E82C3301 (RAW 16)
 */
function GUIDtoHEX($guid)
{
	if (strlen($guid) != 36) return false;

	$parts = explode('-', $guid);
	if (count($parts) != 5) return false;

	if (strlen($parts[0]) != 8) return false;	//Data1
	if (strlen($parts[1]) != 4) return false;	//Data2
	if (strlen($parts[2]) != 4) return false;	//Data3
	if (strlen($parts[3]) != 4) return false;	//Data4
	if (strlen($parts[4]) != 12) return false;	//Data4

	//Data4 stores the bytes in the same order as displayed in the GUID text encoding,
	//but other three fields are reversed on little-endian systems (e.g. Intel CPUs).
	return strrev2($parts[0]).strrev2($parts[1]).strrev2($parts[2]).$parts[3].$parts[4];
}

?>
