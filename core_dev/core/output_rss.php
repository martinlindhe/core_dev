<?php
/**
 * $Id$
 *
 * Simple news feed renderer with support for RSS 2.0 and Atom 1.0
 *
 * RSS 2.0:  http://www.rssboard.org/rss-specification
 * Atom 1.0: http://www.atomenabled.org/developers/syndication
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//TODO: verify that the outputted feeds & mime types actually works with some popular news readers & feed aggregators
//TODO: maybe need to use <![CDATA[text here]]> everywhere too to embed HTML. verify with html entries

//TODO: rename to output_feed

require_once('functions_time.php');	//for date3339() and date882()

class rss_output
{
	var $version = 'core_dev output_feed 1.0'; //XXX version
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
			'<title><![CDATA['.$this->title.']]></title>'.
			'<updated>'.date3339(time()).'</updated>'.
			//optional fields:
			'<link rel="alternate" href="'.$this->link.'"/>'.
			'<generator>'.$this->version.'</generator>';

		foreach ($this->entries as $entry) {
			$res .=
			'<entry>'.
				//required fields:
				'<id>'.trim($entry['link']).'</id>'."\n".
				'<title><![CDATA['.trim($entry['title']).']]></title>'."\n".
				'<updated>'.date3339($entry['pubdate']).'</updated>'."\n".	//RFC 3339 timestamp
				//optional fields:
				'<summary><![CDATA['.trim($entry['desc']).']]></summary>'."\n".
				'<link rel="alternate" href="'.trim($entry['link']).'"/>'."\n".
				'<author><name>'.(!empty($entry['author̈́']) ? $entry['author'] : $this->title).'</name></author>'.
				(!empty($entry['video']) ? '<link rel="enclosure" type="'.$entry['video_type'].'" href="'.urlencode($entry['video']).'"/>' : '')."\n".
				(!empty($entry['image']) ? '<link rel="enclosure" type="'.$entry['image_type'].'" href="'.urlencode($entry['image']).'"/>' : '')."\n".
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
		'<rss version="2.0">'.
			'<channel>'.
				//required fields:
				'<title><![CDATA['.$this->title.']]></title>'.
				'<link>'.$this->link.'</link>'.
				'<description><![CDATA['.$this->desc.']]></description>'.
				//optional fields:
				($this->ttl ? '<ttl>'.$this->ttl.'</ttl>' : '').
				'<generator>core_dev</generator>';				//XXX version

		foreach ($this->entries as $entry) {
			//XXX can only be 1 media object per rss2 item (???)
			$media = '';
			if (!empty($entry['video']))
				$media .= '<enclosure url="'.$entry['video'].'" type="'.$entry['video_type'].'"/>';
			else if (!empty($entry['image']))
				$media .= '<enclosure url="'.$entry['image'].'" type="'.$entry['image_type'].'"/>';

			$res .=
			'<item>'.
				//required fields:
				'<title><![CDATA['.trim($entry['title']).']]></title>'.
				'<link>'.trim($entry['link']).'</link>'.
				'<description><![CDATA['.trim($entry['desc']).']]></description>'.
				//optional fields:
				'<pubDate>'.date882($entry['pubdate']).'</pubDate>'.	//RFC 822 timestamp
				$media.
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
