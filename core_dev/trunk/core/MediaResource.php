<?php
/**
 * $Id$
 *
 * A resource object pointing to a network media resource (audio, video, image)
 */

//STATUS: wip

class MediaResource extends CoreBase
{
    var $track_id;
    var $album_id;
    var $title;

    var $mime;               ///< mimetype of media
    var $thumbnail;          ///< location of thumbnail/cover art
    var $desc;               ///< description

    var $Url;
    var $Duration;
    var $Timestamp;

    function setTimestamp($t) { $this->Timestamp = new Timestamp($t); }
    function setDuration($n) { $this->Duration = new Duration($n); }
    function setUrl($n) { $this->Url = new Url($n); }

    function getTimestamp() { return $this->Timestamp->getUnix(); }
}

?>
