<?php
/**
 * $Id$
 *
 * Simple news feed renderer with support for RSS 2.0 and Atom 1.0
 *
 * Atom 1.0: http://www.atomenabled.org/developers/syndication
 * RSS 2.0:  http://www.rssboard.org/rss-specification
 * RSS 2.0 media rss: http://video.search.yahoo.com/mrss
 *
 * Output verified with http://feedvalidator.org/
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

require_once('output_list.php');
require_once('client_http.php');
require_once('functions_time.php');	//for date3339() and date882()

class output_feed extends core_list
{
	private $version     = 'core_dev output_feed 1.0';
	private $title       = 'Untitled news feed';

	private $desc, $link;
	private $ttl         = 15;    ///< time to live, in minutes
	private $sendHeaders = false; ///< shall we send mime type?

	function setTitle($n) { $this->title = $n; }
	function setLink($n) { $this->link = $n; }
	function enableHeaders() { $this->sendHeaders = true; }
	function disableHeaders() { $this->sendHeaders = false; }

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

		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<feed xmlns="http://www.w3.org/2005/Atom">'.
			'<id>'.htmlspecialchars($this->link).'</id>'.
			'<title><![CDATA['.$this->title.']]></title>'.
			'<updated>'.date3339(time()).'</updated>'.
			'<link rel="self" href="'.htmlspecialchars($u->render()).'"/>'.
			'<generator>'.$this->version.'</generator>'."\n";

		foreach ($this->getEntries() as $entry) {
			$res .=
			'<entry>'.
				'<id>'.(!empty($entry['guid']) ? $entry['guid'] : htmlspecialchars($entry['link']) ).'</id>'.
				'<title><![CDATA['.$entry['title'].']]></title>'.
				'<link rel="alternate" href="'.$entry['link'].'"/>'.
				'<summary><![CDATA['.(!empty($entry['desc']) ? $entry['desc'] : ' ').']]></summary>'.
				'<updated>'.date3339($entry['pubdate']).'</updated>'.
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

			$res .=
			'<item>'.
				'<title><![CDATA['.trim($entry['title']).']]></title>'.
				'<link>'.trim(htmlspecialchars($entry['link'])).'</link>'.
				'<description><![CDATA['.trim($entry['desc']).']]></description>'.
				'<pubDate>'.date882($entry['pubdate']).'</pubDate>'.
				(!empty($entry['guid']) ? '<guid>'.$entry['guid'].'</guid>' : '').
				(!empty($entry['video']) ? '<media:content medium="video" type="'.$entry['video_type'].'" url="'.$entry['video']'"'.(!empty($entry['duration']) ? ' duration="'.$entry['duration'].'"' : '').'/>' : '').
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
