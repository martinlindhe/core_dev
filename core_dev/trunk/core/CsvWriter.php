<?php
/**
 * $Id$
 *
 * CSV writer class
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

require_once('csv_misc.php');

class CsvWriter
{
    private $rows = array();

    function setData($data)
    {
        $this->rows = $data;
    }

    function render()
    {
        $header = array();
        foreach ($this->rows[0] as $idx => $val)
            $header[] = $idx;

        //first row describes the column names
        $res = '; '.implode(', ', $header)."\r\n";

        foreach ($this->rows as $row) {
            $line = array();
            foreach ($row as $col)
                $line[] = csv_escape($col);

            $res .= implode(',', $line)."\r\n";
        }

        return $res;
    }
}

?>
