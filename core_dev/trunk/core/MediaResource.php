<?php
/**
 * $Id$
 *
 * A resource object pointing to a network media resource (audio, video, image)
 */

//STATUS: wip

class AudioResource extends CoreBase
{
    var $track_id;
    var $album_id;
    var $title;

    var $mimetype;           ///< mimetype of media
    var $desc;               ///< description

    var $thumbnail;          ///< location of thumbnail/cover art
    var $thumb_width;
    var $thumb_height;

    var $Url;
    var $Duration;
    var $Timestamp;

    function setTimestamp($t) { $this->Timestamp = new Timestamp($t); }
    function setDuration($n) { $this->Duration = new Duration($n); }
    function setUrl($n) { $this->Url = new Url($n); }
    function getUrl() { return $this->Url->get(); }

    function getTimestamp() { return $this->Timestamp->getUnix(); }
}

class VideoResource extends AudioResource
{
    var $video_height;
    var $video_width;
}

class ImageResource extends CoreBase
{
    var $Url;
    var $width;
    var $height;

    function setUrl($n) { $this->Url = new Url($n); }
    function getUrl() { return $this->Url->get(); }
}

?>
