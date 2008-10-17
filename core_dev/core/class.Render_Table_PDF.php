<?php
/**
 * $Id$
 *
 * Renders a table of data in PDF (.pdf) format
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('output_pdf.php');

class Render_Table_PDF extends Render_Table
{
	function render()
	{
		$out = '';
		$i = 0;
		foreach ($this->data as $data) {
			$out .= '"'.$data.'"';
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
