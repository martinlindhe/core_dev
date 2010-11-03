<?php
/**
 * $Id$
 *
 * Parses an RSS 2.0 feed into NewsItem objects
 *
 * http://www.rssboard.org/rss-specification
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: good

//TODO: extend from CoreXmlReader ??

require_once('class.CoreBase.php');
require_once('NewsItem.php');

require_once('XmlReader.php');

class RssReader extends CoreBase
{
    private $items = array();   ///< list of NewsItem objects
    protected $reader;            ///< XMLReader object
    private $title;             ///< title of the feed

    protected $ext_tags = array(); ///< to be filled with custom tags to parse, set by extending class

    function __construct($data = '')
    {
        if ($data)
            $this->parse($data);
    }

    /**
     * @return array of NewsItem objects
     */
    function getItems() { return $this->items; }

    function getTitle() { return $this->title; }

    /** XXX: MUST be implemented by plugin */
//    function pluginParseTag($key) { }

    function parse($data)
    {
        $this->reader = new CoreXmlReader();
        $this->reader->parse($data);

        while ($this->reader->read())
        {
            if ($this->reader->nodeType != XMLReader::ELEMENT)
                continue;

            switch ($this->reader->name) {
            case 'rss':
                if ($this->reader->getAttribute('version') != '2.0')
                    die('XXX FIXME unsupported RSS version '.$this->reader->getAttribute('version') );
                break;

            case 'channel':
                $this->parseChannel();
                break;

            default:
                echo 'bad top entry '.$this->reader->name.ln();
                break;
            }
        }

        $this->reader->close();
        return true;
    }

    private function parseChannel()
    {
        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'channel')
                return;

            if ($this->reader->nodeType != XMLReader::ELEMENT)
                continue;

            $key = strtolower($this->reader->name);
            switch ($key) {
            case 'title':
                $this->title = $this->reader->readValue();
                break;

            case 'link': break;
            case 'description': break;
            case 'language': break;
            case 'pubdate': break;
            case 'generator': break;
            case 'webmaster': break;
            case 'lastbuilddate': break; //<lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>
            //case 'docs': break; //<docs>http://blogs.law.harvard.edu/tech/rss</docs>
            //case 'managingeditor': break; //<managingEditor>editor@example.com</managingEditor>

            case 'item':
                $this->parseItem();
                break;

            default:
                if (in_array($key, $this->ext_tags)) {
                    $this->pluginParseTag($key);
                } else
                    // echo 'unknown channel entry '.$key.ln();
                break;
            }
        }
    }

    private function parseItem()
    {
        $item = new NewsItem();

        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'item') {
                if ($item->getTitle() == $item->desc) $item->desc = '';
                $this->items[] = $item;
                return;
            }

            if ($this->reader->nodeType != XMLReader::ELEMENT)
                continue;

            $key = strtolower($this->reader->name);

            switch ($key) {
            case 'title':
                $item->setTitle( html_entity_decode($this->reader->readValue(), ENT_QUOTES, 'UTF-8') );
                break;

            case 'description':
                $item->desc = trim( html_entity_decode($this->reader->readValue(), ENT_QUOTES, 'UTF-8') );
                break;

            case 'author':
                $item->author = $this->reader->readValue();
                break;

            case 'link':
                $item->setUrl( $this->reader->readValue() );
                break;

            case 'pubdate':
                $item->setTimestamp( $this->reader->readValue() );
                break;

            case 'guid':
                $item->guid = $this->reader->readValue();
                break;

            case 'media:thumbnail':
                if (!$item->image_url) { //XXX prefer full image over thumbnails
                    $item->image_url  = $this->reader->getAttribute('url');
                    $item->image_mime = file_get_mime_by_suffix($item->image_url);//$this->reader->getAttribute('type')
                }
                break;

            case 'media:content':
                switch ($this->reader->getAttribute('type')) {
                case 'video/x-flv':
                    //XXX HACK: prefer asf (usually mms) over flv (usually over rtmp / rtmpe) because vlc dont support rtmp(e) so well yet (2009.09.23)
                    if (substr($this->reader->getAttribute('url'),0,4) != 'rtmp' || !$item->video_url) {
                        $item->video_url  = $this->reader->getAttribute('url');
                        $item->video_mime = $this->reader->getAttribute('type');

                        $item->setDuration($this->reader->getAttribute('duration'));
                    }
                    break;

                case 'video/x-ms-asf':
                    if (file_suffix($this->reader->getAttribute('url')) == '.asx')
                    {
                        $asx = new input_asx();
                        if ($this->getDebug()) $asx->setDebug();
                        $asx->parse( $this->reader->getAttribute('url') );
                        $list = $asx->getItems();

                        if ($list)
                            $item->video_url = $list[0]->Url->get();
                    } else {
                        $item->video_url = $this->reader->getAttribute('url');
                    }

                    $item->video_mime  = $this->reader->getAttribute('type');
                    $item->setDuration($this->reader->getAttribute('duration'));
                    break;

                case 'video/mp4':
                    //XXX rtsp protocol
                    if (!$item->video_url) {
                        $item->video_url  = $this->reader->getAttribute('url');
                        $item->video_mime = $this->reader->getAttribute('type');
                        $item->setDuration($this->reader->getAttribute('duration'));
                    }
                    break;

                case 'video/3gpp':
                    //XXX rtsp protocol
                    if (!$item->video_url) {
                        $item->video_url  = $this->reader->getAttribute('url');
                        $item->video_mime = $this->reader->getAttribute('type');
                        $item->setDuration($this->reader->getAttribute('duration'));
                    }
                    break;

                case 'video/quicktime':
                    $item->video_url  = $this->reader->getAttribute('url');
                    $item->video_mime = $this->reader->getAttribute('type');
                    $item->setDuration($this->reader->getAttribute('duration'));
                    break;

                case 'image/jpeg':
                    $item->image_url  = $this->reader->getAttribute('url');
                    $item->image_mime = $this->reader->getAttribute('type');
                    break;

                case 'text/html':
                    //<media:content type="text/html" medium="document" url="http://svt.se/2.22620/1.1652031/krigsfartyg_soker_efter_arctic_sea">
                    break;

                case 'application/vnd.apple.mpegurl':
                    //points to a nested m3u playlist, didnt research more
                    //<media:content duration="91" expression="sample" height="360" lang="sv" type="application/vnd.apple.mpegurl" width="640" medium="video" url="http://www0.c90910.dna.qbrick.com/90910/od/20100221/abc_2010-0221-SL-hts-a-v1/abc_2010-0221-SL-hts-a-v1_vod.m3u8">
                    break;

                default:
                    echo 'RssReader->parseItem() unknown MEDIA:CONTENT: '.$this->reader->getAttribute('type').ln();
                    break;
                }
                break;

            default:
                if (in_array($key, $this->ext_tags)) {
                    $this->pluginParseTag($key);
                } else {
                    //echo 'unknown item entry ' .$this->reader->name.ln();
                }
                break;
            }
        }
    }

}

?>
