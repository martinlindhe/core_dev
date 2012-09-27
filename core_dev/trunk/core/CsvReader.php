<?php
/**
 * $Id$
 *
 * Simple CSV (Comma-separated values) data parser
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: simplify & speed up by using php's str_getcsv(), fgetcsv()

namespace cd;

class CsvReader
{
    /**
     * Parses string of CSV data into an array
     */
    static function parse($data, $start_line = 0, $delimiter = ',')
    {
        $res = array();

        $rows = explode("\n", $data);

        $line = 0;
        foreach ($rows as $row)
        {
            if ($line >= $start_line && $row)
                $res[] = self::parseRow($row, $delimiter);

            $line++;
        }

        return $res;
    }

    /**
     * @param $filename string filename
     * @param $callback string callback function
     * @param $start_line int starting line number (counting from 0)
     * @param $delimiter character separating CSV cells (usually , or ;)
     */
    static function parseFile($filename, $callback, $start_line = 0, $delimiter = ',')
    {
        $fp = fopen($filename, 'r');
        if (!$fp || !function_exists($callback)) {
            echo "FATAL: csvParse() callback not defined\n";
            return false;
        }

        $cols = 0;
        $line = 0;
        while (!feof($fp))
        {
            $buf = fgets($fp, 4096);
            if ($line >= $start_line)
            {
                if (!$buf) break;

                $row = self::parseRow($buf, $delimiter);
                if (!$cols)
                    $cols = count($row);

                if ($cols != count($row)) {
                    echo "FATAL: CSV format error in $filename at line ".($line+1).": ".count($row)." columns found, $cols expected\n";
                    d($row);
                    return false;
                }
                if ($row)
                    call_user_func($callback, $row);
            }
            $line++;
        }

        fclose($fp);
    }

    /**
     * Parses a row of CSV data into a array
     *
     * @param $row line of raw CSV data to parse
     * @param $delimiter character separating CSV cells (usually , or ;)
     * @return array of parsed values
     */
    static function parseRow($row, $delimiter = ',')
    {
        if (strpos($row, $delimiter) === false) {
            echo "FATAL: csvParseRow() got bad input\n";
            d($row);
            return false;
        }

        $el = 0;
        $res = array();
        $in_esc = false;

        for ($i=0; $i<strlen($row); $i++)
        {
            if (!isset($res[$el]))
                $res[$el] = '';

            $c = substr($row, $i, 1);

            switch ($c) {
            case $delimiter:
                if (!$in_esc) $el++;
                else $res[$el] .= $c;
                break;

            case '"':
                $in_esc = !$in_esc;
                $res[$el] .= $c;
                break;

            default:
                $res[$el] .= $c;
            }
        }

        //Clean up escaped fields
        for ($i=0; $i<count($res); $i++)
            $res[$i] = self::unescape($res[$i]);

        return $res;
    }

    /**
     * Unescapes CSV data
     * @param $str string to unescape
     * @return unescaped string
     */
    static function unescape($str)
    {
        if (substr($str, -1) == "\r" || substr($str, -1) == "\n")
            $str = rtrim($str); //strip lf

        if (substr($str, 0, 1) == '"' && substr($str, -1) == '"')
            $str = substr($str, 1, -1);

        //embedded double-quote characters must be represented by a pair of double-quote characters.
        $str = str_replace('""', '"', $str);

        return $str;
    }

}

?>
