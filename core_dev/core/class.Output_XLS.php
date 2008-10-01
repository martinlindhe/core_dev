<?php
/**
 * $Id$
 *
 * Functions to create a Microsoft Excel compatible spreadsheet file (.xls)
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

class XLS
{
	private $labels = array();
	private $numbers = array();

	function label($row, $col, $text)
	{
		$this->labels[] = array($row, $col, $text);
	}

	function number($row, $col, $val)
	{
		$this->numbers[] = array($row, $col, $val);
	}

	function write($filename)
	{
		$fp = fopen($filename, 'wb');

		//Begin Of File marker
		fwrite($fp, pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0));

		foreach ($this->labels as $label) {
			//Writes a label (text)
			//FIXME support unicode strings, see pg 18 in Excel97-2007BinaryFileFormat(xls)Specification.xps
			$len = strlen($label[2]);
			fwrite($fp, pack("ssssss", 0x204, 8 + $len, $label[0], $label[1], 0x0, $len));
			fwrite($fp, $label[2]);
		}

		foreach ($this->numbers as $number) {
			//Writes a Number (double)
			fwrite($fp, pack("sssss", 0x203, 14, $number[0], $number[1], 0x0));
			fwrite($fp, pack("d", $number[2]));
		}

		//End Of File marker
		fwrite($fp, pack("ss", 0x0A, 0x00));

		fclose($fp);
	}
}

?>
