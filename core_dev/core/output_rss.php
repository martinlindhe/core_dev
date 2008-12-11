<?php
/**
 * $Id$
 *
 * Simple news feed renderer with support for RSS 2.0 and Atom 1.0
 *
 * RSS 2.0:  http://www.rssboard.org/rss-specification
 * Atom 1.0: http://www.atomenabled.org/developers/syndication
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

//TODO: verify that the outputted feeds & mime types actually works with some popular news readers & feed aggregators

require_once('functions_time.php');	//for date3339() and date882()

class rss_output
{
	var $entries = array();

	var $ttl = 15;	///< time to live, in minutes
	var $title = 'Untitled news feed';
	var $desc = '';
	var $link = '';

	/**
	 * Adds a array of entries to the feed list
	 */
	function addList($list)
	{
		foreach ($list as $entry) {
			$this->entries[] = $entry;
		}
	}

	/**
	 * Adds a entry to the feed list
	 */
	function addEntry($entry)
	{
		$this->entries[] = $entry;
	}

	/**
	 * Renders the feed in Atom 1.0 format
	 */
	function renderATOM()
	{
		$res =
		'<?xml version="1.0" encoding="UTF-8"?>'.
		'<feed xmlns="http://www.w3.org/2005/Atom">'.
			//required fields:
			'<id>'.$this->link.'</id>'.
			'<title>'.$this->title.'</title>'.
			'<updated>2003-12-13T18:30:02Z</updated>'.
			//optional fields:
			'<link href="'.$this->link.'"/>'.
			'<generator>core_dev</generator>';	//XXX version

		foreach ($this->entries as $entry) {
			$res .=
			'<entry>'.
				//required fields:
				'<id>'.trim($entry['link']).'</id>'.
				'<title>'.trim($entry['title']).'</title>'.
				'<updated>'.date3339($entry['pubdate']).'</updated>'.	//RFC 3339 timestamp
				//optional fields:
				'<link href="'.trim($entry['link']).'"/>'.
				'<summary>'.trim($entry['desc']).'</summary>'.
			'</entry>';
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
		'<rss version="2.0">'.
			'<channel>'.
				//required fields:
				'<title>'.$this->title.'</title>'.
				'<link>'.$this->link.'</link>'.
				'<description>'.$this->desc.'</description>'.
				//optional fields:
				($this->ttl ? '<ttl>'.$this->ttl.'</ttl>' : '').
				'<generator>core_dev</generator>';				//XXX version

		foreach ($this->entries as $entry) {
			$res .=
			'<item>'.
				//required fields:
				'<title>'.trim($entry['title']).'</title>'.
				'<link>'.trim($entry['link']).'</link>'.
				'<description>'.trim($entry['desc']).'</description>'.
				//optional fields:
				'<pubDate>'.date882($entry['pubdate']).'</pubDate>'.	//RFC 822 timestamp
				//<enclosure> is used to attach a media object to the feed
			'</item>';
		}

		$res .=
			'</channel>'.
		'</rss>';

		return $res;
	}

	/**
	 * Generates RSS feed
	 */
	function render($format = 'rss2')
	{
		switch ($format) {
			case 'atom':
				return $this->renderATOM();

			case 'rss2':
				return $this->renderRSS2();
		}
		return false;
	}

	/**
	 * Outputs the feed and set HTTP header
	 */
	function output($format = 'rss2')
	{
		switch ($format) {
			case 'atom':
				header('Content-type: application/atom+xml');
				break;

			case 'rss2':
				header('Content-type: application/rss+xml');
				break;
		}

		echo $this->render($format);
	}
}

?>
