<?php
/**
 * INI file input/output class
 *
 * http://en.wikipedia.org/wiki/INI_file
 */

//TODO handle ""-escaped values
//TODO handle escape sequences  "\;", "\=" etc, see wiki article

//TODO optimize disk r/w, currently the whole file is parsed on every get() / set()

class ini
{
	private $filename;

	function __construct($filename)
	{
		$this->filename = $filename;
	}

	function get($section, $key)
	{
		if (!file_exists($this->filename)) return false;

		$lines = file($this->filename, FILE_IGNORE_NEW_LINES);

		$current_section = '';
		$current_key     = '';

		foreach ($lines as $line_num => $l) {

			if (substr($l, 0, 1) == '[' && substr($l, -1) == ']')
				$current_section = substr($l, 1, -1);

			if (strpos($l, '=') !== false) {
				list($current_key, $val) = explode('=', $l, 2);

				if ($current_section == $section && $current_key == $key)
					return $val;
			}
		}
	}

	function set($section, $key, $val)
	{
		die('ini WIP-rewrite parser');

		if (!file_exists($this->filename)) $lines = array();
		else $lines = file($this->filename, FILE_IGNORE_NEW_LINES);

		$changed         = false;
		$current_section = '';
		$current_key     = '';

		foreach ($lines as $line_num => $l) {

			if (substr($l, 0, 1) == '[' && substr($l, -1) == ']') {
				if ($current_section == $section) {
					die('XXX appenda val på slutet av section'); //XXX sker aldrig om filen bara innehåller en sektion

					$changed = true;
				}
				$current_section = substr($l, 1, -1);
			}

			if (strpos($l, '=') !== false) {
				list($current_key, $current_val) = explode('=', $l, 2);

				if ($current_section == $section && $current_key == $key) {
					echo "XXX UPDATE val=".$val." from ".$current_val."\n";
					$lines[$line_num] = $current_key.'='.$val;
					$changed = true;
				}
			}
		}

		if (!$changed) {
			//om ingen append/update, insert at end
			$lines[] = '['.$section.']';
			$lines[] = $key.'='.$val;
		}

		print_r($lines);
		die('XXX write to disk');
	}

}

?>
