<?php
/**
 * $Id$
 *
 * Functions to create a Comma-Separated Values file (.csv)
 *
 * http://en.wikipedia.org/wiki/Comma-separated_values
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

class CSV
{
	private $EOL = "\r\n";		//DOS style line endings
	private $separator = ",";
	private $columns = 3;

	private $data = array();

	/**
	 * Set the number of columns (fields) in the file
	 */
	function setColumns($val)
	{
		$this->columns = $val;
	}

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
	 * Add a value to the data buffer
	 */
	function add($val)
	{
		$this->data[] = $val;
	}

	/**
	 * Outputs the data to specified file
	 */
	function write($filename)
	{
		$fp = fopen($filename, 'w');

		$i = 0;
		foreach ($this->data as $data) {
			fwrite($fp, '"'.$data.'"');	//FIXME only escape with " if string contains $this->separator (?)
			$i++;
			if ($i == $this->columns) {
				fwrite($fp, $this->EOL);
				$i = 0;
			} else {
				fwrite($fp, $this->separator);
			}
		}

		fclose($fp);
	}
}
