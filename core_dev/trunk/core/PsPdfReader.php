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
    }

    function loadFromFile($filename)
    {
        if (!is_file($filename))
            throw new Exception ('file not found');

        $this->data = file_get_contents($filename);
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

            $t = explode(' ', $row, 2);

            switch ($t[0]) {
            case 'BT': $in_chunk = true;  break;
            case 'ET': $in_chunk = false; $chunk_cnt++; break;
            default:
                if ($in_chunk) {
                    $chunks[ $chunk_cnt ] [] = $row;
                } else {
                    echo "Unknown: ".$row."\n";
                }
            }
        }

        d($chunks);

    }

}

?>
