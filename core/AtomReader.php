<?php
/**
 * $Id$
 *
 * Parses an Atom 1.0 feed into NewsItem objects
 *
 * http://www.atomenabled.org/developers/syndication/
 * http://en.wikipedia.org/wiki/Atom_(standard)
 *
 * @author Martin Lindhe, 2008-2012 <martin@ubique.se>
 */

//STATUS: good

//TODO: extend from HttpClient

namespace cd;

require_once('CoreBase.php');
require_once('HttpClient.php');
require_once('NewsItem.php');
require_once('MediaResource.php');

class AtomReader extends CoreBase
{
    private $items = array();
    private $reader;            ///< XMLReader object
    private $title;             ///< feed title
    private $category;          ///< feed category

    /**
     * @return array of NewsItem objects
     */
    function getItems() { return $this->items; }

    function getTitle() { return $this->title; }

    function getCategory() { return $this->category; }

    function parse($data)
    {
        if (is_url($data)) {
            $u = new HttpClient($data);
            $data = $u->getBody();

            //FIXME check http client return code for 404
            if (strpos($data, '<feed ') === false) {
                //dp('AtomReader->parse FAIL: cant parse feed from '.$u->getUrl() );
                throw new \Exception ('AtomReader->parse FAIL: cant parse feed from '.$u->getUrl() );
                return false;
            }
        }

        $this->reader = new \XMLReader();
        $this->reader->xml($data);

        while ($this->reader->read())
        {
            if ($this->reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch ($this->reader->name) {
            case 'feed':
                if ($this->reader->getAttribute('xmlns') != 'http://www.w3.org/2005/Atom')
                    throw new \Exception ('Unknown atom xmlns: '.$this->reader->getAttribute('xmlns') );
                break;

            case 'entry':
                $this->parseEntry();
                break;

            case 'id': break;
            case 'title':
                $this->reader->read();
                $this->title = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
                break;

            case 'category': // <category term="Nyheter" />
                $this->category = $this->reader->getAttribute('term');
                break;

            case 'link': break;
            case 'generator': break;
            case 'updated': break;

            default:
                //XXX: may include openSearch:itemsPerPage (twitter does for example)
                // echo 'bad top entry '.$this->reader->name.ln();
                break;
            }
        }

        $this->reader->close();
        return true;
    }

    private function parseEntry()
    {
        $item = new NewsItem();

        while ($this->reader->read()) {
            if ($this->reader->nodeType == \XMLReader::END_ELEMENT && $this->reader->name == 'entry') {
                if ($item->title == $item->desc) $item->desc = '';
                $this->items[] = $item;
                return;
            }

            if ($this->reader->nodeType != \XMLReader::ELEMENT)
                continue;

            switch (strtolower($this->reader->name)) {
            case 'title':
                $this->reader->read();
                $item->title = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
                break;

            case 'summary':
                $this->reader->read();
                $item->desc = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
                break;

            case 'content':
                $this->reader->read();
                $item->body = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
                break;

            case 'updated':
                $this->reader->read();
                $item->setTimestamp( $this->reader->value );
                break;

            case 'name': ///XXX :hack to avoid 2-level parsing of <author><name>Sveriges Radio</name></author>
                $this->reader->read();
                $item->author = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
                break;

            case 'id':
                $this->reader->read();
                $item->guid = $this->reader->value;
                break;

            case 'link':
                switch ($this->reader->getAttribute('rel')) {
                case 'alternate':
                    $item->setUrl( $this->reader->getAttribute('href') );
                    break;
                case 'enclosure':
                    switch ($this->reader->getAttribute('type')) {
                    case 'video/x-flv':
                    case 'video/quicktime':
                        $item->video_url  = $this->reader->getAttribute('href');
                        $item->video_mime = $this->reader->getAttribute('type');
                        if ($this->reader->getAttribute('length')) $this->duration = $this->reader->getAttribute('length');
                        break;

                    case 'image/jpeg':
                        $item->image_url  = $this->reader->getAttribute('href');
                        $item->image_mime = $this->reader->getAttribute('type');
                        break;

                    default:
                        throw new \Exception ('unknown enclosure mime: '.$this->reader->getAttribute('type') );
                    }
                    break;

                case 'image':
                    switch ($this->reader->getAttribute('type')) {
                    case 'image/png':
                        $img = new ImageResource();
                        $img->setUrl(  $this->reader->getAttribute('href'));
                        $img->setMimetype( $this->reader->getAttribute('type') );
                        $item->addMedia($img);
                        break;

                    default:
                        throw new \Exception ('unknown image mime: '.$this->reader->getAttribute('type') );
                    }
                    break;

                case 'replies':
                    //FIXME: handle
                    break;
                case 'edit': //XXX ???
                case 'self': //XXX ???
                case '': // no "rel" attribute exists
                    break;
                default:
                    d($item);
                    throw new \Exception ('unknown link type: '.$this->reader->getAttribute('rel') );
                }
                break;

            default:
                // echo 'unknown entry entry: '.$this->reader->name.ln();
                break;
            }
        }
    }

}

?>
