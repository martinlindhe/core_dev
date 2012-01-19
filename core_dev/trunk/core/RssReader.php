<?php
/**
 * $Id$
 *
 * Parses an RSS 2.0 feed into NewsItem objects
 *
 * http://www.rssboard.org/rss-specification
 *
 * @author Martin Lindhe, 2008-2012 <martin@startwars.org>
 */

//STATUS: good

//TODO: rewrite to use simplexml ?

require_once('CoreBase.php');
require_once('NewsItem.php');

require_once('XmlReader.php');

class RssReader extends CoreBase
{
    protected $items = array();    ///< list of NewsItem objects
    protected $reader;             ///< CoreXmlReader object
    protected $title;              ///< title of the feed

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
                    throw new Exception ('unsupported RSS version '.$this->reader->getAttribute('version') );
                break;

            case 'channel':
                $this->parseChannel();
                break;

            default:
                throw new Exception ('bad top entry '.$this->reader->name);
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
            case 'lastbuilddate': break;    // <lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>
            case 'docs': break;             // <docs>http://blogs.law.harvard.edu/tech/rss</docs>
            case 'managingeditor': break;   // <managingEditor>editor@example.com</managingEditor>

            case 'item':
                $this->parseItem();
                break;

            default:
                if (in_array($key, $this->ext_tags)) {
                    $this->pluginParseTag($key);
                } else {
                    // echo 'unknown channel entry '.$key.ln();
                }
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

            default:
                if (in_array($key, $this->ext_tags)) {
                    $this->pluginParseTag($key, $item);
                } else {
                    //echo 'unknown item entry ' .$this->reader->name.ln();
                }
                break;
            }
        }

        $this->items[] = $item;
    }

}

?>
