<?php

require_once('class.Render_Table_CSV.php');
require_once('class.Render_Table_PDF.php');
require_once('class.Render_Table_XLS.php');
require_once('class.Render_Table_XHTML.php');

abstract class Render_Table
{
	protected $columns = 3;
	protected $data = array();

	/**
	 * Set the number of columns (fields) in the file
	 */
	function setColumns($val)
	{
		$this->columns = $val;
	}

//TODO ability to add heading data

	/**
	 * Add a value to the data buffer
	 */
	function add($val)
	{
		$this->data[] = $val;
	}

	/**
	 * Renders & stores table in specified file
	 */
	function write($filename)
	{
		$fp = fopen($filename, 'w');
		fwrite($fp, $this->render());
		fclose($fp);
	}

	/**
	 * Returns rendered table
	 *
	 * \return Rendered data in desired format
	 */
	abstract function render();
}
?>
