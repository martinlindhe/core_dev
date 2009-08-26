<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('class.Render_Table_CSV.php');
//require_once('class.Render_Table_PDF.php');	//FIXME not complete
require_once('class.Render_Table_XHTML.php');
require_once('class.Render_Table_XLS.php');

abstract class Render_Table
{
	protected $columns = 3;
	protected $heads = array();
	protected $data = array();

	protected $default_mime = '';	///< default mime type for browser output - FIXME implement
	protected $default_ext = '';	///< default filename extension for output to file

	/**
	 * Set the number of columns (fields) in the file
	 *
	 * @param $n number of columns
	 */
	function setColumns($n)
	{
		//ignored if heading() dictates other
		if (!$this->heads) {
			$this->columns = $n;
		}
	}

	/**
	 * Add headers
	 *
	 * @param $heads List of heading elements
	 */
	function heading($heads = array())
	{
		$this->columns = count($heads);

		$this->heads = $heads;
	}

	/**
	 * Add a value to the data buffer
	 *
	 * @param $val value
	 */
	function add($val)
	{
		$this->data[] = $val;
	}

	/**
	 * Renders & stores table in specified file
	 *
	 * @param $filename file name
	 */
	function write($filename)
	{
		if (strpos($filename, '.') === false) {
			$filename .= $this->default_ext;
		}

		$fp = fopen($filename, 'w');
		fwrite($fp, $this->render());
		fclose($fp);
	}

	/**
	 * Returns rendered table
	 *
	 * @return Rendered data in desired format
	 */
	abstract function render();
}
?>