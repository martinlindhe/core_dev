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

    function getMedia() { return $this->media; }

    function getUrl() { return $this->Url->get(); }
    function getDuration() { return $this->Duration->get(); }
    function getTitle() { return $this->title; }

    function addMediaItems($arr)
    {
        foreach ($arr as $o)
            $this->addMedia($o);
    }

    function addMedia($o)
    {
        if ($o instanceof VideoResource || $o instanceof ImageResource)
            $this->media[] = $o;
        else {
            d($o);
            throw new Exception ('unhandled class '.get_class($o) );
        }
    }

    function renderMediaDetails()
    {
        foreach ($this->media as $m) {
            if ($m instanceof VideoResource)
                echo $m->renderDetails().'<br/>';
        }
    }

    /**
     * Selects best quality video out of all available VideoResource
     */
    function getHiQualityVideo()
    {
        $best_w = 0;
        $best = false;

        foreach ($this->media as $m)
            if ($m instanceof VideoResource)
                if ($m->video_width > $best_w) {
                    $best_w = $m->video_width;
                    $best = $m;
                }

        return $best;
    }

    /**
     * Selects (best) thumbnail from ImageResource
     */
    function getThumbnailLink()
    {
    }


}

?>
