<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

class NewsItem
{
    private $title;
    var $desc;
    var $author;

    var $guid;
    var $image_mime;
    var $image_url;
    var $video_mime;
    var $video_url;

    var $Duration;  ///< video duration
    private $Timestamp;
    private $Url;       ///< location of news article

    function setTime($s) { $this->Timestamp = new Timestamp($s); }
    function setUrl($s) { $this->Url = new Url($s); }

    function setTitle($s) { $this->title = $s; }

    function getTime() { return $this->Timestamp; }
    function getUrl() { return $this->Url; }
    function getTitle() { return $this->title; }

    function __construct()
    {
        $this->Duration  = new Duration();
    }
}

?>