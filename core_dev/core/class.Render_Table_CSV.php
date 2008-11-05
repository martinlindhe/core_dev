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

require_once('output_csv.php');

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

	function render()
	{
		$out = '';

		if ($this->heads) {
			$i = 0;
			foreach ($this->heads as $h) {
				$out .= csvEscape($h);
				$i++;
				if ($i < $this->columns) {
					$out .= $this->separator;
				}
			}
			$out .= $this->EOL;
		}

		$i = 0;
		foreach ($this->data as $data) {
			$out .= csvEscape($data);
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
