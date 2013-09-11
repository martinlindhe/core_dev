<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

namespace cd;

require_once('RssReader.php');
require_once('M3uReader.php'); /// for MediaRssReader

class MediaRssReader extends RssReader
{
    function __construct($data = '')
    {
        $this->ext_tags = array(
        'media:thumbnail', 'media:group', 'media:content', //XXX inside <item> tag
        );

        parent::__construct($data);
    }

    // @param @&$item points to RssReader NewsItem object
    function pluginParseTag($key, &$item)
    {
        switch ($key) {
        // <media:thumbnail url="http://media.svt.se/download/mcc/flash/20101013/PG-1135459-007A-UPPDRAGGRANSKN/PG-1135459-007A-UPPDRAGGRANSKNI-02_thumb_0.jpg" width="128" height="72" />
        case 'media:thumbnail':
            $thumb = new ImageResource();
            $thumb->setUrl(  $this->reader->getAttribute('url') );
            $thumb->width  = $this->reader->getAttribute('width');
            $thumb->height = $this->reader->getAttribute('height');
            $item->addMedia($thumb);
            break;

        case 'media:group': //container, holds 1 or more <media:content> descriptions, different versions of same media
            break;

        case 'media:content':
            $this->parseMediaContent($item);
            break;

        default:
            echo 'xxxxx '.$key."\n";
            break;
        }
    }

    private function parseMediaContent(&$item)
    {
        switch ($this->reader->getAttribute('medium')) {
        // <media:content bitrate="1800.0" duration="3475" expression="full" height="144" lang="sv" type="video/3gpp" width="176" medium="video" url="rtsp://www...3gp">
        case 'video':
            $mime = $this->reader->getAttribute('type');

            if ($mime == 'application/vnd.apple.mpegurl') {
                // http url with a m3u playlist, possibly containing more m3u playlists
                $m3u = new M3uReader( $this->reader->getAttribute('url') );

                //throw new \Exception ('xxxxxxx apple stuff');
                foreach ( $m3u->getItems() as $m) {
                    if (file_suffix($m->getUrl()) == '.m3u8') {
                        // pl points to another playlist
                        $m3u2 = new M3uReader($m->getUrl() );

//                        d ( $m3u2->getItems() );
                        $item->addMediaItems( $m3u2->getItems() );
                    }
                }
                return;
            }

            $media = new VideoResource();
            $media->setUrl(        $this->reader->getAttribute('url') );
            $media->setDuration(   $this->reader->getAttribute('duration') );
            $media->video_height = $this->reader->getAttribute('height');
            $media->video_width  = $this->reader->getAttribute('width');
            $media->mimetype     = $mime;
            $media->bitrate      = $this->reader->getAttribute('bitrate');
            break;

        // <media:content height="576" type="image/jpeg" width="720" medium="image" url="http://www....jpg" />
        case 'image':
            $media = new ImageResource();
            $media->setUrl(  $this->reader->getAttribute('url') );
            $media->width  = $this->reader->getAttribute('width');
            $media->height = $this->reader->getAttribute('height');
            break;

        default:
            throw new \Exception ('unhandled medium type '.$this->reader->getAttribute('medium') );
        }

        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'media:content') {

//                d($media);
                $item->addMedia($media);
                return;
            }

            if ($this->reader->nodeType != XMLReader::ELEMENT)
                continue;

            $key = strtolower($this->reader->name);

            switch ($key) {
            // <media:player url="http://svtplay.se/v/2191821/uppdrag_granskning/del_7_av_16_kapade_natverk" />
            case 'media:player':
                break;

            // <media:title>Uppdrag granskning - Del 7 av 16: Kapade nätverk</media:title>
            case 'media:title':
                $title = $this->reader->readValue();  //XXXX never used!
                break;

            default:
                //echo 'unknown item entry ' .$this->reader->name.ln();
                break;
            }
        }

        $item->addMedia($media);
    }

}

?>
