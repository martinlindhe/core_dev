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

//WIP: handle <rss xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">

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
        $data = trim($data);
        if (!$data)
            return false;

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
                throw new Exception ('bad top entry: '.$this->reader->name);
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
                $item->setTitle( trim($this->reader->readValue()) );
                break;

            case 'description':
                $item->desc = trim($this->reader->readValue());
                break;
            case 'content:encoded': // XXX non standard tag
                $item->body = trim($this->reader->readValue());
                break;

            case 'author':
            case 'dc:creator': // XXX non standard tag
                $item->author = $this->reader->readValue();
                break;

            case 'link':
                $url = $this->reader->readValue();
                // UGLY HACKS - FIX invalid URLs
                // no need to do this if value was not parsed to a Url object
                $url = str_replace("\xC2\xA0", '%C2%A0', $url); // non-breaking space

                $url = str_replace("\xC3\xA4", '%C3%A4', $url); // ä
                $url = str_replace("\xC3\xA5", '%C3%A5', $url); // å
                $url = str_replace("\xC3\xA9", '%C3%A9', $url); // é
                $url = str_replace("\xC3\xB6", '%C3%B6', $url); // ö
                $url = str_replace("\xC3\xBC", '%C3%BC', $url); // ü
                $item->setUrl( $url );
                break;

            case 'pubdate':
            case 'dc:date': // XXX non standard tag, <dc:date>2012-02-01 05:50:00</dc:date>
                $item->setTimestamp( $this->reader->readValue() );
                break;

            case 'guid':
                $item->guid = $this->reader->readValue();
                break;

            case 'category':
                $item->category = $this->reader->readValue();
                break;

            default:
                if (in_array($key, $this->ext_tags)) {
                    $this->pluginParseTag($key, $item);
                } else {
                    // echo 'unknown item entry ' .$this->reader->name.ln();
                }
                break;
            }
        }

        $this->items[] = $item;
    }

}

?>
