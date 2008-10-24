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
 * @param $filename string filename
 * @param $callback string callback function
 * @param $start_line int starting line number
 */
function csvParse($filename, $callback, $start_line = 0)
{
	$fp = fopen($filename, 'r');
	if (!$fp || !function_exists($callback)) return false;

	$cols = 0;
	$i = 0;
	while (!feof($fp)) {
		$buffer = fgets($fp, 4096);

		if ($i >= $start_line && strpos($buffer, ',') !== false) {
			$arr = explode(',', $buffer);
			if (!$cols) $cols = count($arr);

			if ($cols != count($arr)) {
				echo "FATAL: CSV format error in $filename at line $i\n";
				return false;
			}
			call_user_func($callback, $arr);
		}

		$i++;
	}
	fclose($fp);
}

?>
