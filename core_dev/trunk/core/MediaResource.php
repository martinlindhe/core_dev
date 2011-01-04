<?php
/**
 * $Id$
 *
 * A resource object pointing to a network media resource (audio, video, image)
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

abstract class MediaResource extends CoreBase
{
    var $track_id;
    var $album_id;
    var $title;

    var $mimetype;           ///< mimetype of media
    var $desc;               ///< description

    var $Url;

    function setUrl($n) { $this->Url = new Url($n); }
    function getUrl() { return $this->Url->get(); }
    function setMimetype($s) { $this->mimetype = $s; }
}

class AudioResource extends MediaResource
{
    var $thumbnail;          ///< location of thumbnail/cover art
    var $thumb_width;
    var $thumb_height;

    var $bitrate;

    var $Duration;
    var $Timestamp;

    function setTimestamp($t) { $this->Timestamp = new Timestamp($t); }
    function setDuration($n) { $this->Duration = new Duration($n); }

    function getTimestamp() { return $this->Timestamp->getUnix(); }
}

class VideoResource extends AudioResource
{
    var $video_height;
    var $video_width;

    function renderDetails()
    {
        return $this->video_height.'x'.$this->video_width.' '.$this->bitrate.' bps ('.$this->mimetype.') '.$this->getUrl();
    }
}

class ImageResource extends MediaResource
{
    var $Url;
    var $width;
    var $height;

    function setUrl($n) { $this->Url = new Url($n); }
    function getUrl() { return $this->Url->get(); }
}

?>
