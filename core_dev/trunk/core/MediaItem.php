<?php

class MediaItem extends CoreBase //XXX rename to PlaylistItem ?
{
    var $title;
    var $mime;               ///< mimetype of media
    var $thumbnail;          ///< location of thumbnail/cover art
    var $desc;               ///< description

    var $Duration;           ///< duration of media
    var $Timestamp;
    var $Url;                ///< location of media

    function __construct()
    {
        $this->Duration  = new Duration();
        $this->Timestamp = new Timestamp();
        $this->Url       = new Url();
    }

    function setDuration($s) { $this->Duration = new Duration($s); }
    function setTimestamp($s) { $this->Timestamp = new Timestamp($s); }
}

?>
