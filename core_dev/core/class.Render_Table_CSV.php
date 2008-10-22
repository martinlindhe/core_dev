<?php
/**
 * $Id$
 *
 * Renders a table of data in Comma-Separated Values (.csv) format
 *
 * http://en.wikipedia.org/wiki/Comma-separated_values
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

class Render_Table_CSV extends Render_Table
{
	private $EOL = "\r\n";		//DOS style line endings
	private $separator = ",";

	protected $default_ext = '.csv';

	/**
	 * Set the line ending characters (usually "\r\n" or "\n")
	 */
	function setEOL($val)
	{
		$this->EOL = $val;
	}

	/**
	 * Set the value separator character (usually "," or ";")
	 */
	function setSeparator($val)
	{
		$this->separator = $val;
	}

	/**
	 * Escapes data if nessecary
	 */
	private function escape($str)
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

	function render()
	{
		$out = '';

		if ($this->heads) {
			$i = 0;
			foreach ($this->heads as $h) {
				$out .= $this->escape($h);
				$i++;
				if ($i < $this->columns) {
					$out .= $this->separator;
				}
			}
			$out .= $this->EOL;
		}

		$i = 0;
		foreach ($this->data as $data) {
			$out .= $this->escape($data);
			$i++;
			if ($i == $this->columns) {
				$out .= $this->EOL;
				$i = 0;
			} else {
				$out .= $this->separator;
			}
		}
		return $out;
	}
}

?>
