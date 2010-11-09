<?php
/**
 * $Id$
 *
 * M3U playlist format reader
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('HttpClient.php');

class M3uReader
{
    private $entries = array();

    function __construct($data = '')
    {
        if ($data)
            $this->parse($data);
    }

    function parse($data)
    {
        if (is_url($data)) {
            $http = new HttpClient($data);
            $http->setCacheTime(60 * 60); //1h
            $data = $http->getBody();
d($data);
        }

        $this->entries = array();

        $rows = explode("\n", $data);

        foreach ($rows as $row)
        {
            $p = explode(':', $row, 2);
            switch ($p[0]) {
            case '#EXTM3U': case '': break;
            case '#EXTINF':
                $x = explode(',', $p[1], 2);
                $ent['length'] = ($x[0] != '-1' ? $x[0] : '');
                $ent['title']  = $x[1];
                break;

            default:
                $ent['link'] = $row;
                $this->entries[] = $ent;
                unset($ent);
                break;
            }
        }
    }

    function getEntries() { return $this->entries; }
}

?>
