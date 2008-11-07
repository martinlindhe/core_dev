<?php
/**
 * $Id$
 *
 * Simple CSV parser
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */


/**
 * CSV data parser
 *
 * @param $filename string filename
 * @param $callback string callback function
 * @param $start_line int starting line number
 * @param $delimiter character separating CSV cells (usually , or ;)
 */
function csvParse($filename, $callback, $start_line = 0, $delimiter = ',')
{
	$fp = fopen($filename, 'r');
	if (!$fp || !function_exists($callback)) return false;

	$cols = 0;
	$i = 0;
	while (!feof($fp)) {
		$buffer = fgets($fp, 4096);
		if ($i >= $start_line) {
			if (!$buffer) break;
			$row = csvParseRow($buffer, $delimiter);
			if (!$cols) $cols = count($row);
			if ($cols != count($row)) {
				echo "FATAL: CSV format error in $filename at line ".($i+1).": ".count($row)." columns found, $cols expected\n";
				return false;
			}
			if ($row) call_user_func($callback, $row);
		}
		$i++;
	}
	fclose($fp);
}

/**
 * Parses a row of CSV data into a array
 *
 * @param $row line of raw CSV data to parse
 * @param $delimiter character separating CSV cells (usually , or ;)
 */
function csvParseRow($row, $delimiter = ',')
{
	if (strpos($row, $delimiter) === false) return false;

	$el = 0;
	$res = array();
	$in_esc = false;

	for ($i=0; $i<strlen($row); $i++) {
		if (!isset($res[$el])) $res[$el] = '';
		$c = substr($row, $i, 1);
		switch ($c) {
			case $delimiter:
				if (!$in_esc) $el++;
				else $res[$el] .= $c;
				break;

			case '"':
				$in_esc = !$in_esc;
				$res[$el] .= $c;
				break;

			default:
				$res[$el] .= $c;
		}
	}

	//Clean up escaped fields
	for ($i=0; $i<count($res); $i++) {
		$res[$i] = csvUnescape($res[$i]);
	}

	return $res;
}

/**
 * Unescapes CSV data
 */
function csvUnescape($str)
{
	if (substr($str, 0, 1) == '"' && substr($str, -1) == '"') {
		$str = substr($str, 1, -1);
	}

	//embedded double-quote characters must be represented by a pair of double-quote characters.
	$str = str_replace('""', '"', $str);
	return $str;
}

?>
