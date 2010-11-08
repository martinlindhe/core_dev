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

    protected $Duration;  ///< video duration
    protected $Timestamp;
    protected $Url;       ///< location of news article

    protected $media = array();

    function __construct()
    {
        $this->Timestamp = new Timestamp();
        $this->Duration  = new Duration();
        $this->Url       = new Url();
    }

    function setTimestamp($s) { $this->Timestamp = new Timestamp($s); }
    function setUrl($s) { $this->Url = new Url($s); }
    function setDuration($s) { $this->Duration = new Duration($s); }

    function setTitle($s) { $this->title = $s; }

    function getTimestamp() { return $this->Timestamp; }

    function getUrl() { return $this->Url->get(); }
    function getDuration() { return $this->Duration->get(); }
    function getTitle() { return $this->title; }

    function addMedia($o)
    {
        if ($o instanceof VideoResource || $o instanceof ImageResource)
            $this->media[] = $o;
        else
            throw new Exception ('unhandled class '.get_class($o) );
    }

    /**
     * Selects best quality video out of all available VideoResource
     */
    function getVideoUrlBestQuality()
    {
        //d($this->media);
        //die;
    }

    /**
     * Selects (best) thumbnail from ImageResource
     */
    function getThumbnailLink()
    {
    }


}

?>
