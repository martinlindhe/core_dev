<?php
/**
 * $Id$
 *
 * M3U playlist format reader
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('HttpClient.php');

class M3uReader
{
    private $items = array();

    function __construct($data = '')
    {
        if ($data)
            $this->parse($data);
    }

    function parse($data)
    {
        $base_url = '';

        if (is_url($data)) {
//d($data);
            $http = new HttpClient($data);
            $http->setCacheTime(60 * 60); //1h
            $data = $http->getBody();

            if (strpos($data, '#EXTM3U') === false) {
                throw new Exception ('M3uReader->parse FAIL: cant parse feed from '.$http->getUrl() );
                return false;
            }
            $base_url = dirname( $http->getUrl() );
        }

        $this->items = array();

        $rows = explode("\n", $data);

        $ent = new VideoResource();

//echo '<pre>';
        foreach ($rows as $row)
        {
            $row = trim($row);
            $p = explode(':', $row, 2);

            switch ($p[0]) {
            case '#EXTM3U': case '': break;

            /*
            #EXT-X-VERSION:2
            #EXT-X-ALLOW-CACHE:YES
            #EXT-X-TARGETDURATION:10
            #EXT-X-MEDIA-SEQUENCE:0
            */

            case '#EXTINF':
                $x = explode(',', $p[1], 2);
                $ent->setDuration($x[0] != '-1' ? $x[0] : '');
                $ent->setTitle($x[1]);
                break;

            // multiple quality streams for same media can exist
            case '#EXT-X-STREAM-INF':  // #EXT-X-STREAM-INF:PROGRAM-ID=1, BANDWIDTH=294332
                $x = explode(',', $p[1]);
                foreach ($x as $kv) {
                    $x2 = explode('=', trim($kv), 2);
                    if ($x2[0] == 'PROGRAM-ID')
                        $ent->track_id = $x2[1];

                    if ($x2[0] == 'BANDWIDTH') // XXX BITRATE???
                        $ent->bitrate = $x2[1];
                }

                break;

            default:
                if (substr($row, 0, 1) == '#')
                    break;

                if ($base_url && strpos($row, '://') === false)
                    $row = $base_url.'/'.$row;

                $ent->setUrl($row);
                $this->items[] = $ent;
                $ent = new VideoResource();
                break;
            }
        }
    }

    function getItems() { return $this->items; }
}

?>
