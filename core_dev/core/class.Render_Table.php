<?php

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
	 */
	function setColumns($val)
	{
		//ignored if heading() dictates other
		if (!$this->heads) {
			$this->columns = $val;
		}
	}

	/**
	 * Add headers
	 */
	function heading($heads = array())
	{
		$this->columns = count($heads);

		$this->heads = $heads;
	}

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
	 * \return Rendered data in desired format
	 */
	abstract function render();
}
?>
