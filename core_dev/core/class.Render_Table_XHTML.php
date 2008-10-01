<?php
/**
 * $Id$
 *
 * Functions to create a XHTML table
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

class XHTML
{
	private $columns = 3;		//std
	private $data = array();	//std

	/**
	 * Set the number of columns (fields) in the file
	 */
	function setColumns($val)			//std funct
	{
		$this->columns = $val;
	}

	/**
	 * Add a value to the data buffer
	 */
	function add($val)					//std funct
	{
		$this->data[] = $val;
	}

	/**
	 * Outputs the data to specified file
	 */
	function write($filename = 'stdout')	//std function. FIXME rename to "render". add "output" function to write to file
	{
		$fp = fopen($filename, 'w');

		fwrite($fp, '<table border="1">');

		$i = 0;
		foreach ($this->data as $data) {
			if ($i == 0) {
				fwrite($fp, '<tr>');
			}
			fwrite($fp, '<td>'.$data.'</td>');	//FIXME only escape with " if string contains $this->separator (?)
			$i++;
			if ($i == $this->columns) {
				fwrite($fp, '</tr>');
				$i = 0;
			}
		}

		fwrite($fp, '</table>');
		fclose($fp);
	}
}
