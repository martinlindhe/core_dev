<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

class NewsItem
{
    var $title;
    var $desc;
    var $author;

    var $guid;
    var $image_mime;
    var $image_url;
    var $video_mime;
    var $video_url;

    var $Duration;  ///< video duration
    var $Timestamp;
    var $Url;       ///< location of news article

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
}

?>
