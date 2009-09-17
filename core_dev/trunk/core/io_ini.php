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

	/**
	 * Returns ini content parsed, used by set()
	 */
	function getAsArray()
	{
		if (!file_exists($this->filename)) return false;

		$lines = file($this->filename, FILE_IGNORE_NEW_LINES);

		$current_section = '';
		$current_key     = '';
		$res = array();

		foreach ($lines as $line_num => $l) {

			if (substr($l, 0, 1) == '[' && substr($l, -1) == ']') {
				$current_section = substr($l, 1, -1);
				$res[ $current_section ] = array();
			}

			if (strpos($l, '=') !== false) {
				list($current_key, $val) = explode('=', $l, 2);

				$res[ $current_section ][ $current_key ] = $val;
			}
		}

		return $res;
	}

	function set($section, $key, $val)
	{
		$data = $this->getAsArray();

		$data[ $section ][ $key ] = $val;

		$out = '';

		foreach ($data as $section => $values) {

			$out .= "[".$section."]\n";

			foreach ($values as $key => $val)
				$out .= $key."=".$val."\n";

			$out .= "\n";
		}

		file_put_contents($this->filename, $out);
	}

}

?>
