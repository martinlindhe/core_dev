<?php
/**
 * $Id$
 *
 * Creates a RSS feed from a array
 *
 * RSS 2.0: http://www.rssboard.org/rss-specification
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */


//TODO: rss 1.0 output
//TODO: atom output

class rss_output
{
	var $entries = array();

	var $ttl = 15;	///< time to live, in minutes
	var $title = 'Untitled RSS 2.0 feed';
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

	function renderATOM()
	{
		//XXX implement
	}

	function renderRSS1()
	{
		//XXX implement
	}

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
				'<title>'.$entry['title'].'</title>'.
				'<link>'.$entry['link'].'</link>'.
				'<description>'.$entry['desc'].'</description>'.
				//optional fields:
				'<pubDate>'.date('r', $entry['pubdate']).'</pubDate>'.
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

			case 'rss1':
				return $this->renderRSS1();

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
		//header()   application/rss+xml
		echo $this->render($format);
	}
}

?>
