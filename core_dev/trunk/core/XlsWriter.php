<?php
/**
 * $Id$
 *
 * XLS writer class (Microsoft Excel Spreadsheet)
 *
 * Parts of the code was based on http://px.sklar.com/code.html/id=488 by Christian Novak
 *
 * The output file has been tested successfully with:
 *
 *        Microsoft Office 2003
 *        Open Office 2.4, 3.1
 *
 * https://secure.wikimedia.org/wikipedia/en/wiki/Microsoft_Excel_file_format
 *
 * Mime-Type: application/vnd.ms-excel
 * Extension: .xls
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip
//FIXME write header rows in bold text

class XlsWriter
{
    private $data = array();
    private $write_header = true;
    private $row = 0;
    private $col = 0;

    function setData($data) { $this->data = $data; }

    /** Beginning Of File marker */
    private function bof() { return pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); }

    /** End Of File marker */
    private function eof() { return pack("ss", 0x0A, 0x00); }

    /** Writes a number */
    function writeNumber($val)
    {
        //FIXME this don't output good enough precision, seem to round to 2 decimals (at least Open Office)
        return pack("sssssd", 0x203, 14, $this->row, $this->col, 0x0, $val);    //0x203 = double
    }

    /** Writes a text string */
    function writeText($s)
    {
        //FIXME support unicode strings, see pg 18 in Excel97-2007BinaryFileFormat(xls)Specification.xps
        $len = strlen($s);
        return pack("ssssss", 0x204, 8 + $len, $this->row, $this->col, 0x0, $len) . $s;    //0x204 = label
    }

    function render()
    {
        $res = $this->bof();

        //first row contains the column names
        if ($this->write_header) {
            $header = array();
            foreach ($this->data[0] as $idx => $val) {
                $res .= $this->writeText($idx);
                $this->col++;
            }
            $this->row++;
        }

        foreach ($this->data as $row) {
            $this->col = 0;
            foreach ($row as $val) {
                if (is_numeric($val))
                    $res .= $this->writeNumber($val);
                else
                    $res .= $this->writeText($val);
                $this->col++;
            }
            $this->row++;
        }

        $res .= $this->eof();
        return $res;
    }
}

?>
