<?php
/**
 * $Id$
 *
 * CSV writer class
 *
 * Output using separator ";" and eol "\r\n" is tested & works with
 * OpenOffice 3.1, Gnumeric 1.9.9 and Microsoft Excel xxx
 *
 * http://en.wikipedia.org/wiki/Comma-separated_values
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

class CsvWriter
{
    private $rows         = array();
    private $separator    = ';';
    private $eol          = "\r\n";
    private $write_header = true;

    function setData($data) { $this->rows = $data; }

    /**
     * Escapes data if nessecary
     */
    function escape($s)
    {
        //Fields with embedded double-quote characters must be delimited with double-quote characters,
        // and the embedded double-quote characters must be represented by a pair of double-quote characters.
        if (strpos($s, '"') !== false) {
            return '"'.str_replace('"', '""', $s).'"';
        }

        //Fields with embedded commas or line breaks must be delimited with double-quote characters.
        //Fields with leading or trailing spaces must be delimited by double-quote characters.
        if (
            strpos($s, $this->separator) !== false ||
            strpos($s, "\r") !== false || strpos($s, "\n") !== false ||
            substr($s, 0, 1)  == ' '   || substr($s, -1)    == ' ' ||
            substr($s, 0, 1)  == "\t"  || substr($s, -1)    == "\t")
        {
            return '"'.$s.'"';
        }
        return $s;
    }

    function render()
    {
        $res = '';

        //first row contains the column names
        if ($this->write_header) {
            $header = array();
            foreach ($this->rows[0] as $idx => $val)
                $header[] = $this->escape($idx);

            $res .= implode($this->separator, $header).$this->eol;
        }

        foreach ($this->rows as $row) {
            $line = array();
            foreach ($row as $col)
                $line[] = $this->escape($col);

            $res .= implode($this->separator, $line).$this->eol;
        }

        return $res;
    }
}

?>
