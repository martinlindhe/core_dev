<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

namespace cd;

class NewsItem extends CoreBase
{
    var $title;
    var $desc;            ///< article summary / intro
    var $body;            ///< main article
    var $author;
    var $guid;
    var $category;        ///< text description

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
        $best_width = 0;
        $best_bitrate = 0;
        $best_id = false;
        $best_type = '';

        foreach ($this->media as $m)
            if ($m instanceof VideoResource)
            {
                $cur_type = get_protocol($m->Url);

                if ($m->bitrate > $best_bitrate) {
                    $best_bitrate = $m->bitrate;
                    $best_id = $m;
                    $best_type = get_protocol($m->Url);
                    // echo 'setting to bitrate '.$best_bitrate.', type '.$best_type."\n";
                } else if ($m->video_width > $best_width) {
                    $best_width = $m->video_width;
                    $best_id = $m;
                    $best_type = get_protocol($m->Url);
                    // echo 'setting to width '.$best_w.', type '.$best_type."\n";
                } else if ($cur_type != $best_type && !in_array( $cur_type, array('http', 'mms', 'rtsp'))) {
                    $best_type = get_protocol($m->Url);
                    $best_id = $m;
                    // echo 'setting to type '.$best_type."\n";
                }
            }

        return $best_id;
    }

    /**
     * Selects (best) thumbnail from ImageResource
     */
    function getThumbnailLink()
    {
        throw new Exception ('implement me!');
    }

}

?>
