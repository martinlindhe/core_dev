<?php
/**
 * $Id$
 *
 * A resource object pointing to a network media resource (audio, video, image)
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

abstract class MediaResource extends CoreBase  //XXXX delete this class
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

    function setTitle($s) { $this->title = $s; }
    function getTitle() { return $this->title; }
}

class AudioResource extends MediaResource  //XXX stop extending MediaResource
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

class ImageResource
{
    var $title;
    var $desc;

    var $Url;
    var $width;
    var $height;

    var $image_id;
    var $type;  // example "poster", "cover"

    var $mimetype;           ///< mimetype of media

    function setUrl($n) { $this->Url = new Url($n); }
    function getUrl() { return $this->Url->get(); }
    function setMimetype($s) { $this->mimetype = $s; }
/*
    static function getBestImage($objs, $type = 'poster')
    {
        foreach ($objs as $o)
        {
            if (!($o instanceof ImageResource))
                throw new \Exception ('unhandled object type');

            if ($o->type != $type)
                continue;

            echo html_img($o->Url->get())."<br/>\n";
//            d($o);
        }
    }
*/

}

class ArtistResource
{
    var $name;
    var $musicbrainz_id;
    var $summary;          ///< quick summary of artist
    var $detailed_info;    ///< more details of artist
    var $images = array(); ///< array of ImageResource objects
    var $tags   = array(); ///< array of strings, "tags" such as music genre
}

class BookResource
{
    var $title;
    var $authors;
    var $publisher;
    var $isbn10;
    var $isbn13;
}

?>
