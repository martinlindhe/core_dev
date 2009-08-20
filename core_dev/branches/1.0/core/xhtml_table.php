<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//TODO: update class.Render_Table_XHTML.php to use this class
//TODO: ajax callback stuff
//TODO: js-reorder by column stuff

class xhtml_table
{
	var $headers = array();  ///< contains all headers
	var $mapping = array();
	var $arr     = array();

	function __construct()
	{
	}

	function header()
	{
		for ($i=0; $i < func_num_args(); $i++) {
			$this->headers[] = func_get_arg($i);
		}
	}

	/**
	 * Maps the content of a indexed array to corresponding columns as defined by header()
	 *
	 * @param $arr array to map
	 * @param $idx1 index 1 to map to header 1
	 * @param $idx2 index 2 to map top header 2
	 */
	function mapArray()
	{
		$this->arr = func_get_arg(0);

		for ($i=1; $i < func_num_args(); $i++) {
			$this->mapping[] = func_get_arg($i);
		}
	}

	function render()
	{
		if (count($this->headers) != count($this->mapping) || !count($this->headers)) {
			echo "FATAL: xhtml_table misuse!";
			return;
		}

		echo '<table>';

		echo '<tr>';
		for ($i=0; $i < count($this->headers); $i++) {
			echo '<th>'.$this->headers[$i].'</th>';
		}
		echo '</tr>';

		foreach ($this->arr as $row) {
			echo '<tr>';
			for ($i=0; $i < count($this->headers); $i++) {
				echo '<td>'.$row[ $this->mapping[$i] ].'</td>';
			}
			echo '</tr>';
		}

		echo '</table>';
	}
}

?>
