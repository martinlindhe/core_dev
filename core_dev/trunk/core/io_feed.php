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

require_once('class.Timestamp.php');

require_once('client_http.php');

require_once('input_rss.php');
require_once('input_atom.php');

require_once('input_asx.php'); //XXX: FIXME use in io_playlist instead
require_once('input_m3u.php'); //XXX: FIXME use in io_playlist


//TODO: rewrite/merge output_feed class with NewsFeed class
class NewsFeed
{
	private $sort = true; ///< sort output array
	private $http;

	function __construct()
	{
		$this->http = new http();
	}

	/**
	 * @param $s cache time in seconds; max 2592000 (30 days)
	 */
	function setCacheTime($s) { $this->http->setCacheTime($s); }

	function setSort($bool) { $this->sort = $bool; }

	function getList($url, $callback = '')
	{
		if (!is_url($url)) return false;

		$data = $this->http->get($url);

		$entries = $this->parse($data, $callback);
		if (!$entries) return false;

		if ($this->sort) uasort($entries, array($this, 'sortListDesc'));

		return $entries;
	}

	/**
	 * Parses input $data if autodetected
	 */
	function parse($data, $callback = '')
	{
		if (strpos($data, '<rss ') !== false) {
			$rss = new input_rss($data, $callback);
			return $rss->getEntries();
		} else if (strpos($data, '<feed ') !== false) {
			$atom = new input_atom($data, $callback);
			return $atom->getEntries();
		} else if (strpos($data, '<asx ') !== false) {
			$asx = new input_asx($data, $callback);
			return $asx->getEntries();
		} else if (strpos($data, '#EXTM3U') !== false) {
			$m3u = new input_m3u($data, $callback);
			return $m3u->getEntries();
		}

		echo "input_feed->parse error: unhandled feed: ".substr($data, 0, 200)." ...".dln();
		return false;
	}

	/**
	 * List sort filter
	 * @return Internal list, sorted descending by published date
	 */
	private function sortListDesc($a, $b)
	{
		if (empty($a['pubdate']) || empty($b['pubdate'])) return -1;

		return ($a['pubdate'] > $b['pubdate']) ? -1 : 1;
	}

}


class output_feed
{
	private $version     = 'core_dev output_feed 1.0';
	private $title       = 'Untitled news feed';
	private $entries     = array();
	private $desc;
	private $link;

	private $ttl         = 15;    ///< time to live, in minutes
	private $sendHeaders = false; ///< shall we send mime type?

	function getEntries() { return $this->entries; }

	function setTitle($n) { $this->title = $n; }
	function setLink($n) { $this->link = $n; }
	function enableHeaders() { $this->sendHeaders = true; }
	function disableHeaders() { $this->sendHeaders = false; }

	/**
	 * Adds a array of entries to the feed list
	 */
	function addList($list)
	{
		foreach ($list as $entry)
			$this->entries[] = $entry;
	}

	/**
	 * Adds a entry to the feed list
	 */
	function addEntry($entry)
	{
		$this->entries[] = $entry;
	}

	/**
	 * Generates XML for feed
	 */
	function render($format = 'rss2')
	{
		switch ($format) {
		case 'atom':
			if ($this->sendHeaders) header('Content-type: application/atom+xml');
			return $this->renderATOM();

		case 'rss2':
			if ($this->sendHeaders) header('Content-type: application/rss+xml');
			return $this->renderRSS2();
		}
		return false;
	}

	/**
	 * Renders the feed in Atom 1.0 format
	 */
	function renderATOM()
	{
		$u = new http($this->link);
		$u->setPath($_SERVER['REQUEST_URI']);

		$ts = new Timestamp();

		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<feed xmlns="http://www.w3.org/2005/Atom">'.
			'<id>'.htmlspecialchars($this->link).'</id>'.
			'<title><![CDATA['.$this->title.']]></title>'.
			'<updated>'.$ts->getRFC3339().'</updated>'.
			'<link rel="self" href="'.htmlspecialchars($u->render()).'"/>'.
			'<generator>'.$this->version.'</generator>'."\n";

		foreach ($this->getEntries() as $entry) {
			$ts->set($entry['pubdate']);
			$res .=
			'<entry>'.
				'<id>'.(!empty($entry['guid']) ? $entry['guid'] : htmlspecialchars($entry['link']) ).'</id>'.
				'<title><![CDATA['.$entry['title'].']]></title>'.
				'<link rel="alternate" href="'.$entry['link'].'"/>'.
				'<summary><![CDATA['.(!empty($entry['desc']) ? $entry['desc'] : ' ').']]></summary>'.
				'<updated>'.$ts->getRFC3339().'</updated>'.
				'<author><name>'.(!empty($entry['author̈́']) ? $entry['author'] : $this->title).'</name></author>'.
				//XXX no way to embed video duration, <link length="x"> is length of the resource, in bytes.
				(!empty($entry['video']) ? '<link rel="enclosure" type="'.$entry['video_type'].'" href="'.$entry['video'].'"/>' : '').
				(!empty($entry['image']) ? '<link rel="enclosure" type="'.$entry['image_type'].'" href="'.$entry['image'].'"/>' : '').
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
		$u = new http($this->link);
		$u->setPath(!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');

		$ts = new Timestamp();

		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">'.
			'<channel>'.
				'<title><![CDATA['.$this->title.']]></title>'.
				'<link>'.htmlspecialchars($this->link).'</link>'.
				'<description><![CDATA['.$this->desc.']]></description>'.
				($this->ttl ? '<ttl>'.$this->ttl.'</ttl>' : '').
				'<atom:link rel="self" type="application/rss+xml" href="'.htmlspecialchars($u->render()).'"/>'.
				'<generator>'.$this->version.'</generator>'."\n";

		foreach ($this->getEntries() as $entry) {

			$ts->set($entry['pubdate']);
			$res .=
			'<item>'.
				'<title><![CDATA['.trim($entry['title']).']]></title>'.
				'<link>'.trim(htmlspecialchars($entry['link'])).'</link>'.
				'<description><![CDATA['.trim($entry['desc']).']]></description>'.
				'<pubDate>'.$ts->getRFC882().'</pubDate>'.
				(!empty($entry['guid']) ? '<guid>'.$entry['guid'].'</guid>' : '').
				(!empty($entry['video']) ? '<media:content medium="video" type="'.$entry['video_type'].'" url="'.$entry['video'].'"'.(!empty($entry['duration']) ? ' duration="'.$entry['duration'].'"' : '').'/>' : '').
				(!empty($entry['image']) ? '<media:content medium="image" type="'.$entry['image_type'].'" url="'.$entry['image'].'"/>' : '').
			'</item>'."\n";
		}

		$res .=
			'</channel>'.
		'</rss>';

		return $res;
	}
}

?>
