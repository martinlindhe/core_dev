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
 * Parts of the code is based on http://px.sklar.com/code.html/id=488 by Christian Novak
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

class Render_Table_XLS extends Render_Table
{
	function render()
	{
		//Begin Of File marker
		$out = pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);

		$row = 0;
		$col = 0;
		foreach ($this->data as $data) {
			if (is_numeric($data)) {
				//Writes a Number (double)
				$out .= pack("sssss", 0x203, 14, $row, $col, 0x0);
				$out .= pack("d", $data);
			} else {
				//Writes a label (text)
				//FIXME support unicode strings, see pg 18 in Excel97-2007BinaryFileFormat(xls)Specification.xps
				$len = strlen($data);
				$out .= pack("ssssss", 0x204, 8 + $len, $row, $col, 0x0, $len);
				$out .= $data;
			}
			$col++;
			if ($col == $this->columns) {
				$row++;
				$col = 0;
			}
		}

		//End Of File marker
		$out .= pack("ss", 0x0A, 0x00);
		return $out;
	}
}

?>
