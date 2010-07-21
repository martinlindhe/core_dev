<?php
/**
 * $Id$
 *
 * Postscript (PDF subset) reader
 *
 * Parses a subset of postscript used in PDF files
 *
 * PostScript Language Reference, Third Edition:
 * http://partners.adobe.com/public/developer/en/ps/PLRM.pdf
 *
 * + supplement:
 * http://partners.adobe.com/public/developer/en/ps/PS3010and3011.Supplement.pdf
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: VERY WIP

/*
2. Extract the content for each page. Each content stream is essentially the script
   portion of a traditional PostScript program using very specific procedures,
   such as m for moveto and l for lineto.
*/

class PsPdfReader
{
    private $data;

    function __construct($data = '')
    {
        if (is_file($data))
            $this->loadFromFile($data);
        else
            $this->loadFromData($data);
    }

    function loadFromFile($filename)
    {
        if (!is_file($filename))
            throw new Exception ('file not found');

        $data = file_get_contents($filename);

        $this->loadFromData($data);
    }

    function loadFromData($data)
    {
        //postscript is stored as latin1, convert to utf-8
        $data = mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');

        $this->data = $data;
    }

    function parse()
    {
        if (!$this->data)
            throw new Exception ('no ps data loaded');

        $rows = explode("\n", $this->data);

        $chunks = array();
        $chunk_cnt = 0;
        $in_chunk = false;

        foreach ($rows as $row) {
            //echo $row."\n";

            $t = explode(' ', $row, 2);

            switch ($t[0]) {
            case 'BT': $in_chunk = true;  break;
            case 'ET': $in_chunk = false; $chunk_cnt++; break;
            default:
                if ($in_chunk) {
                    //chunks contained inside "BT" and "ET" tags
                    $chunks[ $chunk_cnt ] [] = $row;
                } else {
                    //echo "XXX Unknown: ".$row."\n";
                }
            }
        }

        //XXX hack, just strips control chars- may break

        $txt = array();
        foreach ($chunks as $c) {
            //XXX hack, all lines is wrapped inside (text)Tj", strip it away
            if (substr($c[2], -3) == ')Tj' && substr($c[2], 0, 1) == '(')
                $txt[] = substr($c[2], 1, -3);
            else
                $txt[] = $c[2];
        }
        return $txt;
    }

}

?>
