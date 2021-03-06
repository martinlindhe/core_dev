<?php
/**
 * $Id$
 *
 * Simple newsfeed (RSS, Atom) reader/writer with support for RSS 2.0 and Atom 1.0
 *
 * @author Martin Lindhe, 2008-2012 <martin@ubique.se>
 */

namespace cd;

require_once('CoreList.php');
require_once('Duration.php');
require_once('Url.php');
require_once('Timestamp.php');
require_once('NewsItem.php');
require_once('RssReader.php');
require_once('AtomReader.php');
require_once('HttpClient.php');

abstract class FeedWriter extends CoreList
{
    protected $version   = 'core_dev NewsFeed 1.0';
    protected $title     = 'Untitled news feed';
    protected $author    = 'unknown';  ///< feed author
    protected $desc;
    protected $url       = '';      ///< full url to this feed
    protected $ttl       = 15;      ///< time to live, in minutes
    protected $TimeUpdated;         ///< timestamp of last updated time, if set

    function getTitle() { return $this->title; }

    function setTitle($n) { $this->title = $n; }
    function setUrl($n) { $this->url = $n; }
    function setAuthor($s) { $this->author = $s; }

    function setTimeUpdated($ts) { $this->TimeUpdated = new Timestamp($ts); }

    /**
     * Adds a entry to the feed list
     */
    function addItem($i)
    {
        switch (get_class($i)) {
        case 'cd\NewsItem':
            $item = $i;
            break;

        case 'cd\VideoResource':
d($i);die;
    //    d($i);
            //convert into a NewsItem
            $item = new NewsItem();

            $item->title        = $i->title;
            $item->desc         = $i->desc;
            $item->image_url    = $i->thumbnail;
            $item->image_mime   = file_get_mime_by_suffix($i->thumbnail);

            $item->Url      ->set($i->Url->get() );
            $item->Duration ->set($i->Duration->get() );
            $item->Timestamp->set($i->Timestamp->get() );
            break;

        default:
            throw new \Exception ('cant handle '.get_class($i) );
        }
        parent::addItem($item);
    }

    /**
     * Loads input data from RSS or Atom feeds into NewsItem entries
     */
    function load($data)
    {
        if (is_array($data)) {
            $this->addItems( $data);
            return;
        }

        if (is_url($data)) {
            $http = new HttpClient($data);
            if ($this->getDebug()) $http->setDebug();
            $data = $http->getBody();
        }

        if (strpos($data, '<rss ') !== false) {
            $feed = new RssReader();
        } else if (strpos($data, '<feed ') !== false) {
            $feed = new AtomReader();
        } else {
            echo 'NewsFeed->load error: unhandled feed: '.substr($data, 0, 100).' ...'.ln();
            return false;
        }

        if ($this->getDebug()) $feed->setDebug();
        $feed->parse($data);
        $this->title = $feed->getTitle();
        $this->addItems( $feed->getItems() );
    }

    /**
     * Sorts the list
     */
    function sort($order = 'desc')
    {
        switch ($order) {
        case 'asc':  die('FIXME implement ascending sort'); break;
        case 'desc': $callback = array($this, 'sortListDesc'); break;
        default: return false;
        }

        uasort($this->items, $callback);
    }

    /**
     * List sort filter
     * @return Internal list, sorted descending by published date
     */
    private function sortListDesc($a, $b)
    {
        if (!$a->getTimestamp()->getUnix()) return 1;

        return ($a->getTimestamp()->getUnix() > $b->getTimestamp()->getUnix()) ? -1 : 1;
    }

}

?>
