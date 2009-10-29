<?php
/**
 * $Id$
 *
 * Simple newsfeed (RSS, Atom) reader/writer with support for RSS 2.0 and Atom 1.0
 *
 * Atom 1.0: http://www.atomenabled.org/developers/syndication
 * RSS 2.0:  http://www.rssboard.org/rss-specification
 * RSS 2.0 media rss: http://video.search.yahoo.com/mrss
 *
 * Output verified with http://feedvalidator.org/
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: rewriting, need to rewrite/merge output_feed class with NewsFeed class

require_once('class.Duration.php');
require_once('class.Timestamp.php');

require_once('client_http.php');
require_once('input_rss.php');
require_once('input_atom.php');

class NewsItem
{
	var $title;
	var $desc;
	var $author;
	var $url;
	var $guid;
	var $image_mime;
	var $image_url;
	var $video_mime;
	var $video_url;
	var $Duration;       ///< video duration
	var $Timestamp;

	function __construct()
	{
		$this->Duration  = new Duration();
		$this->Timestamp = new Timestamp();
	}
}

class NewsFeed
{
	private $entries = array(); ///< NewsItem objects

	function getList() { return $this->entries; }

	/**
	 * Loads input data from RSS or Atom feeds into NewsItem entries
	 */
	function load($data)
	{
		if (is_url($data)) {
			$u = new http($data);
			$data = $u->get();
		}

		if (strpos($data, '<rss ') !== false) {
			$feed = new input_rss();
		} else if (strpos($data, '<feed ') !== false) {
			$feed = new input_atom();
		} else {
			echo "NewsFeed->load error: unhandled feed: ".substr($data, 0, 200)." ...".dln();
			return false;
		}

		$feed->parse($data);

		$this->entries = $feed->getItems();
	}

	/**
	 * Sorts the list
	 */
	function sort($callback = '')
	{
		if (!$callback) $callback = array($this, 'sortListDesc');

		uasort($this->entries, $callback);
	}

	/**
	 * List sort filter
	 * @return Internal list, sorted descending by published date
	 */
	private function sortListDesc($a, $b)
	{
		if (!$a->Timestamp->get()) return 1;

		return ($a->Timestamp->get() > $b->Timestamp->get()) ? -1 : 1;
	}

}


class output_feed
{
	private $version   = 'core_dev output_feed 1.0';
	private $title     = 'Untitled news feed';
	private $entries   = array();
	private $desc;
	private $url       = '';    ///< full url to this feed
	private $ttl       = 15;    ///< time to live, in minutes
	private $headers   = true;  ///< shall we send mime type?

	function getItems() { return $this->entries; }

	function setTitle($n) { $this->title = $n; }
	function setUrl($n) { $this->url = $n; }
	function sendHeaders($bool = true) { $this->headers = $bool; }

	/**
	 * Adds a array of entries to the feed list
	 */
	function addList($list)
	{
		foreach ($list as $e)
			$this->addItem($e);
	}

	/**
	 * Adds a entry to the feed list
	 */
	function addItem($e)
	{
		switch (get_class($e)) {
		case 'NewsItem':
			$this->entries[] = $e;
			break;

		case 'MediaItem':
			//convert a MediaItem into a NewsItem
			$item = new NewsItem();

			$item->title          = $e->title;
			$item->desc           = $e->desc;
			$item->image_url      = $e->thumbnail;
			$item->image_mime     = file_get_mime_by_suffix($e->thumbnail);
			$item->video_mime     = $e->mime;
			$item->video_url      = $e->url;
			$item->Duration->set ( $e->Duration->get() );
			$item->Timestamp->set( $e->Timestamp->get() );

			$this->entries[] = $item;
			break;

		default:
			d('output_feed->addItem bad data: ');
			d($e);
			break;
		}
	}

	/**
	 * Generates XML for feed
	 */
	function render($format = 'rss2')
	{
		//use executing location if no feed url is specified
		if (!$this->url)
			$this->url = xhtmlGetUrl();

		switch ($format) {
		case 'atom':
			if ($this->headers) header('Content-type: application/atom+xml');
			return $this->renderATOM();

		case 'rss':
		case 'rss2':
			if ($this->headers) header('Content-type: application/rss+xml');
			return $this->renderRSS2();
		}
		return false;
	}

	/**
	 * Renders the feed in Atom 1.0 format
	 */
	function renderATOM()
	{
		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<feed xmlns="http://www.w3.org/2005/Atom">'.
			'<id>'.htmlspecialchars($this->url).'</id>'.
			'<title><![CDATA['.$this->title.']]></title>'.
			//'<updated>'.$this->Timestamp->getRFC3339().'</updated>'.
			'<link rel="self" href="'.htmlspecialchars($this->url).'"/>'.
			'<generator>'.$this->version.'</generator>'."\n";

		foreach ($this->getItems() as $item)
		{
			//link directly to video if no webpage url was found
			if (!$item->url && $item->video_url)
				$item->url = $item->video_url;

			$res .=
			'<entry>'.
				'<id>'.($item->guid ? $item->guid : htmlspecialchars($item->url) ).'</id>'.
				'<title><![CDATA['.$item->title.']]></title>'.
				'<link rel="alternate" href="'.htmlspecialchars($item->url).'"/>'.
				'<summary><![CDATA['.($item->desc ? $item->desc : ' ').']]></summary>'.
				'<updated>'.$item->Timestamp->getRFC3339().'</updated>'.
				'<author><name>'.$item->author.'</name></author>'.
				//XXX no way to embed video duration, <link length="x"> is length of the resource, in bytes.
				($item->video_url ? '<link rel="enclosure" type="'.$item->video_mime.'" href="'.htmlspecialchars($item->video_url).'"/>' : '').
				($item->image_url ? '<link rel="enclosure" type="'.$item->image_mime.'" href="'.htmlspecialchars($item->image_url).'"/>' : '').
			'</entry>'."\n";
		}
		$res .=
		'</feed>';
		return $res;
	}

	/**
	 * Renders the feed in RSS 2.0 format
	 */
	function renderRSS2()
	{
		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">'.
			'<channel>'.
				'<title><![CDATA['.$this->title.']]></title>'.
				'<link>'.htmlspecialchars($this->url).'</link>'.
				'<description><![CDATA['.$this->desc.']]></description>'.
				($this->ttl ? '<ttl>'.$this->ttl.'</ttl>' : '').
				'<atom:link rel="self" type="application/rss+xml" href="'.htmlspecialchars($this->url).'"/>'.
				'<generator>'.$this->version.'</generator>'."\n";

		foreach ($this->getItems() as $item)
		{
			//link directly to video if no webpage url was found
			if (!$item->url && $item->video_url)
				$item->url = $item->video_url;

			$res .=
			'<item>'.
				'<title><![CDATA['.$item->title.']]></title>'.
				'<link>'.htmlspecialchars($item->url).'</link>'.
				'<description><![CDATA['.$item->desc.']]></description>'.
				'<pubDate>'.$item->Timestamp->getRFC882().'</pubDate>'.
				($item->guid ? '<guid>'.$item->guid.'</guid>' : '').
				($item->video_url ? '<media:content medium="video" type="'.$item->video_mime.'" url="'.htmlspecialchars($item->video_url).'"'.($item->Duration->get() ? ' duration="'.$item->Duration->inSeconds().'"' : '').'/>' : '').
				($item->image_url ? '<media:content medium="image" type="'.$item->image_mime.'" url="'.htmlspecialchars($item->image_url).'"/>' : '').
			'</item>'."\n";
		}

		$res .=
			'</channel>'.
		'</rss>';

		return $res;
	}
}

?>
