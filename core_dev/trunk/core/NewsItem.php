<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

class NewsItem extends CoreBase
{
    var $title;
    var $desc;
    var $author;

    var $guid;

    var $Duration;  ///< video duration
    var $Timestamp;
    var $Url;       ///< location of news article

    private $media = array();

    function setTimestamp($s) { $this->Timestamp = new Timestamp($s); }
    function setUrl($s) { $this->Url = new Url($s); }
    function setDuration($s) { $this->Duration = new Duration($s); }

    function setTitle($s) { $this->title = $s; }

    function getTime() { return $this->Timestamp->getUnix(); }

    function getUrl() { return $this->Url->get(); }
    function getDuration() { return $this->Duration->get(); }
    function getTitle() { return $this->title; }

    function __construct()
    {
        $this->Duration  = new Duration();
    }

    function addMedia($o)
    {
        if ($o instanceof VideoResource)
            $this->media[] = $o;
        else
            throw new Exception ('unhandled class '.get_class($o) );
    }

    /**
     * Selects best quality video out of all available video url:s
     */
    function getVideoUrlBestQuality()
    {
        d($this->media);
        die;
    }


}

?>
