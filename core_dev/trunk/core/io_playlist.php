<?php
/**
 * $Id$
 *
 * Renders a playlist in XSPF, PLS or M3U format
 *
 * References
 * ----------
 * http://validator.xspf.org/
 * http://en.wikipedia.org/wiki/Xspf
 * http://en.wikipedia.org/wiki/M3u
 * http://en.wikipedia.org/wiki/PLS_(file_format)
 *
 * http://schworak.com/programming/music/playlist_m3u.asp
 * http://gonze.com/playlists/playlist-format-survey.html
 *
 * XSPF Compatiblity (2009.08.05)
 * ------------------------------
 * ffmpeg/ffplay: dont support xspf playlists but SoC project (but only player for rtmp:// content)
 * VLC 1.0.1: works (not with rtmp:// content)
 * Totem 2.27: trouble loading xspf from certain url's: http://bugzilla.gnome.org/show_bug.cgi?id=590722
 * SMPlayer 0.67: dont support xspf playlists: https://sourceforge.net/tracker/index.php?func=detail&aid=1920553&group_id=185512&atid=913576
 * XBMC dont support xspf playlists: http://xbmc.org/trac/ticket/4763
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: ok

//XXX TODO ability to load playlist from PLS files
//XXX TODO add input_xspf.php support, ability to fetch xspf from web

require_once('prop_Duration.php');
require_once('prop_Location.php');
require_once('prop_Timestamp.php');

require_once('input_asx.php');
require_once('input_m3u.php'); //XXX: TODO support input m3u playlists
require_once('io_newsfeed.php');

require_once('xhtml_header.php');

class MediaItem extends CoreDevBase //XXX rename to PlaylistItem ?
{
	var $title;
	var $mime;               ///< mimetype of media
	var $thumbnail;          ///< location of thumbnail/cover art
	var $desc;               ///< description

	var $Duration;           ///< duration of media
	var $Location;           ///< location of media
	var $Timestamp;

	function __construct()
	{
		$this->Duration  = new Duration();
		$this->Location  = new Location();
		$this->Timestamp = new Timestamp();
	}
}

class Playlist extends CoreDevBase
{
	private $headers = true;                ///< shall we send mime type?
	private $entries = array();             ///< MediaItem objects
	private $title   = 'Untitled playlist'; ///< name of playlist

	function getItems() { return $this->entries; }

	function sendHeaders($bool = true) { $this->headers = $bool; }
	function setTitle($t) { $this->title = $t; }

	/**
	 * Adds a array of items to the feed list
	 *
	 * @param $list list of MediaItem objects
	 */
	function addItems($list)
	{
		foreach ($list as $e)
			$this->addItem($e);
	}

	/**
	 * Adds a item to the feed list
	 */
	function addItem($e)
	{
		switch (get_class($e)) {
		case 'MediaItem':
			$this->entries[] = $e;
			break;

		case 'NewsItem':
			//convert a NewsItem into a MediaItem
			$item = new MediaItem();

			$item->title          = $e->title;
			$item->desc           = $e->desc;
			$item->thumbnail      = $e->image_url;
			$item->mime           = $e->video_mime;
			$item->Duration->set  ( $e->Duration->get() );
			$item->Location->set  ( $e->video_url );
			$item->Timestamp->set ( $e->Timestamp->get() );

			$this->entries[] = $item;
			break;

		default:
			d('Playlist->addItem bad data: ');
			d($e);
			break;
		}
	}

	/**
	 * Loads input data from ASX playlists into MediaItem entries
	 */
	function load($data)
	{
		if (is_url($data)) {
			$u = new HttpClient($data);
			$data = $u->getBody();
		}

		if (strpos($data, '<asx ') !== false) {
			$asx = new input_asx();
			$pl = $asx->parse($data);
		} else {
			echo "Playlist->load error: unhandled feed: ".substr($data, 0, 200)." ...".dln();
			return false;
		}

		$this->entries = $pl->getItems();
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
	 *
	 * @return Internal list, sorted descending by published date
	 */
	private function sortListDesc($a, $b)
	{
		if (!$a->Timestamp->get()) return 1;

		return ($a->Timestamp->get() > $b->Timestamp->get()) ? -1 : 1;
	}

	function render($format = 'xhtml')
	{
		switch ($format) {
		case 'xspf':
			if ($this->headers) header('Content-type: application/xspf+xml');
			return $this->renderXSPF();

		case 'm3u':
			if ($this->headers) header('Content-type: audio/x-mpegurl');
			return $this->renderM3U();

		case 'pls':
			if ($this->headers) header('Content-type: audio/x-scpls');
			return $this->renderPLS();

		case 'xhtml':
		case 'html':
			$res = '';
			if ($this->headers) {
				$header = new xhtml_header();
				$header->setTitle($this->title);
				$res = $header->render();
			}

			return $res . $this->renderXHTML();

		case 'atom':
			$feed = new NewsFeed();
			$feed->sendHeaders($this->headers);
			$feed->addItems($this->entries);
			$feed->setTitle($this->title);
			return $feed->render('atom');

		case 'rss2':
		case 'rss':
			$feed = new NewsFeed();
			$feed->sendHeaders($this->headers);
			$feed->addItems($this->entries);
			$feed->setTitle($this->title);
			return $feed->render('rss');
		}

		echo "Playlist->render: unknown format ".$format."\n";
		return false;
	}

	private function renderXSPF()
	{
		$res  = '<?xml version="1.0" encoding="UTF-8"?>';
		$res .= '<playlist version="1" xmlns="http://xspf.org/ns/0/">';
		$res .= '<trackList>'."\n";

		foreach ($this->getItems() as $item)
		{
			$res .= '<track>';
			$title = ($item->Timestamp ? $item->Timestamp->render().' ' : '').$item->title;
			//if ($item->desc) $title .= ' - '.$item->desc;
			$res .= '<title><![CDATA['.trim($title).']]></title>';

			$res .= '<location>'.$item->Location.'</location>';

			if ($item->Duration)
				$res .= '<duration>'.$item->Duration->inMilliseconds().'</duration>';

			if ($item->thumbnail)
				$res .= '<image>'.$item->thumbnail.'</image>';

			$res .= '</track>'."\n";
		}

		$res .= '</trackList>';
		$res .= '</playlist>';

		return $res;
	}

	private function renderM3U()
	{
		$res = "#EXTM3U\n";
		foreach ($this->getItems() as $item)
		{
			$res .=
			"#EXTINF:".($item->Duration ? round($item->Duration->inSeconds(), 0) : '-1').",".($item->title ? $item->title : 'Untitled track')."\n".
			$item->Location."\n";
		}

		return $res;
	}

	private function renderPLS()
	{
		$res =
		"[playlist]\n".
		"NumberOfEntries=".count($this->entries)."\n".
		"\n";

		$i = 0;
		foreach ($this->getItems() as $item)
		{
			$i++;
			$res .=
			"File".  $i."=".$item->Location."\n".
			"Title". $i."=".($item->title ? $item->title : 'Untitled track')."\n".
			"Length".$i."=".($item->Duration ? $item->Duration->inSeconds() : '-1')."\n".
			"\n";
		}
		$res .= "Version=2\n";
		return $res;
	}

	/**
	 * Renders the playlist as a HTML table
	 */
	private function renderXHTML()
	{
		$res = '<table border="1">';

		foreach ($this->getItems() as $item)
		{
			$title = $item->Timestamp ? $item->Timestamp->render().' ' : '';

			$title .=
				($item->Location ? '<a href="'.$item->Location.'">' : '').
				($item->title ? $item->title : 'Untitled entry').
				($item->Location ? '</a>' : '');

			$res .=
			'<tr><td>'.
			'<h2>'.$title.'</h2>'.
			($item->thumbnail ? '<img src="'.$item->thumbnail.'" width="320" style="float: left; padding: 10px;"/>' : '').
			($item->desc ? '<p>'.$item->desc.'</p>' : '').
			($item->Duration ? t('Duration').': '.$item->Duration->render().'<br/>' : '').
			'</td></tr>';
		}

		return $res;
	}

}

?>
