<?php
/**
 * $Id$
 *
 * Renders a table of data in Microsoft Excel Spreadsheet (.xls) format
 *
 * The output file has been tested successfully with:
 *
 *		Microsoft Office 2003
 *		Open Office 2.4
 *
 * FIXME try Office 2007
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('output_xls.php');

class Render_Table_XLS extends Render_Table
{
	function render()
	{
		$out = xlsBOF();

		$row = 0;
		$col = 0;
		foreach ($this->data as $data) {
			if (is_numeric($data)) {
				$out .= xlsWriteDouble($row, $col, $data);
			} else {
				$out .= xlsWriteLabel($row, $col, $data);
			}
			$col++;
			if ($col == $this->columns) {
				$row++;
				$col = 0;
			}
		}

		$out .= xlsEOF();
		return $out;
	}
}

?>
