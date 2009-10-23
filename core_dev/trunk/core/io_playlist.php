<?php
/**
 * $Id$
 *
 * Generates a XSPF, PLS or M3U playlist
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

//XXX TODO ability to load playlist from XSPF, M3U, PLS files
//XXX TODO add input_xspf.php support, ability to fetch xspf from web

require_once('xhtml_header.php');

class MediaItem //XXX rename to PlaylistItem ?
{
	var $title;
	var $url;       ///< location of media
	var $mime;      ///< mimetype of media
	var $duration;  ///< duration of media

	var $thumbnail; ///< location of thumbnail/cover art
	var $time_published;
	var $desc;      ///< description
}

class Playlist
{
	private $sendHeaders = false;               ///< shall we send mime type?

	private $entries     = array();             ///< MediaItem objects
	private $title       = 'Untitled playlist'; ///< name of playlist

	function getItems() { return $this->entries; }

	function setTitle($t) { $this->title = $t; }

	/**
	 * Adds a array of items to the feed list
	 *
	 * @param $list list of MediaItem objects
	 */
	function addList($list)
	{
		foreach ($list as $e)
			$this->addItem($e);
	}

	/**
	 * Adds a item to the feed list
	 */
	function addItem($e)
	{
		if (get_class($e) == 'MediaItem') {
			$this->entries[] = $e;
		} else if (get_class($e) == 'NewsItem') {
			//convert a NewsItem into a MediaItem
			$item = new MediaItem();

			$item->title          = $e->title;
			$item->desc           = $e->desc;
			$item->thumbnail      = $e->image_url;

			$item->mime           = $e->video_mime;
			$item->url            = $e->video_url;
			$item->duration       = $e->duration;
			$item->time_published = $e->time_published;

			$this->entries[] = $item;

		} else {
			d('Playlist->addItem bad data: ');
			d($e);
		}
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
		if (!$a->time_published) return 1;

		return ($a->time_published > $b->time_published) ? -1 : 1;
	}

	function enableHeaders() { $this->sendHeaders = true; }
	function disableHeaders() { $this->sendHeaders = false; }

	function render($format = 'xhtml')
	{
		switch ($format) {
		case 'xspf':
			if ($this->sendHeaders) header('Content-type: application/xspf+xml');
			return $this->renderXSPF();

		case 'm3u':
			if ($this->sendHeaders) header('Content-type: audio/x-mpegurl');
			return $this->renderM3U();

		case 'pls':
			if ($this->sendHeaders) header('Content-type: audio/x-scpls');
			return $this->renderPLS();

		case 'xhtml':
		case 'html':
			return $this->renderXHTML();
		}

		echo "Playlist->render: unknown format ".$format."\n";
		return false;
	}

	function renderXSPF()
	{
		$res  = '<?xml version="1.0" encoding="UTF-8"?>';
		$res .= '<playlist version="1" xmlns="http://xspf.org/ns/0/">';
		$res .= '<trackList>'."\n";

		foreach ($this->getItems() as $item) {

			//XXX: xspf spec dont have a way to add a timestamp for each entry (??)
			//XXX: create categories from $row['category']

			$res .= '<track>';
			$title = ($item->time_published ? formatTime($item->time_published).' ' : '').$item->title;
			//if ($item->desc) $title .= ' - '.$item->desc;
			$res .= '<title><![CDATA['.trim($title).']]></title>';

			$res .= '<location>'.$item->url.'</location>';

			if ($item->duration)
				$res .= '<duration>'.($item->duration*1000).'</duration>'; //in milliseconds

			if ($item->thumbnail)
				$res .= '<image>'.$item->thumbnail.'</image>';

			$res .= '</track>'."\n";
		}

		$res .= '</trackList>';
		$res .= '</playlist>';

		return $res;
	}

	function renderM3U()
	{
		$res = "#EXTM3U\n";
		foreach ($this->getItems() as $item) {
			$res .= "#EXTINF:".($item->duration ? round($item->duration, 0) : '-1').",".$item->title."\n";
			$res .= $item->url."\n";
		}

		return $res;
	}

	function renderPLS()
	{
		$res =
		"[playlist]\n".
		"NumberOfEntries=".count($this->entries)."\n".
		"\n";

		$i = 0;
		foreach ($this->getItems() as $item) {
			$i++;
			$res .=
			"File".  $i."=".$item->url."\n".
			"Title". $i."=".$item->title."\n".
			"Length".$i."=".($item->duration ? $item->duration : '-1')."\n".
			"\n";
		}
		$res .= "Version=2\n";
		return $res;
	}

	/**
	 * Renders the playlist as a HTML table
	 */
	function renderXHTML()
	{
		$header = new xhtml_header();
		$header->setTitle($this->title);
		$res = $header->render();

		$res .= '<table border="1">';

		foreach ($this->getItems() as $item)
		{
			$title = $item->time_published ? formatTime($item->time_published).' ' : '';
			$title .= ($item->url ? '<a href="'.$item->url.'">' : '').$item->title.($item->url ? '</a>' : '');

			$res .=
			'<tr><td>'.
			'<h2>'.$title.'</h2>'.
			($item->thumbnail ? '<img src="'.$item->thumbnail.'" width="320" style="float: left; padding: 10px;"/>' : '').
			($item->desc ? '<p>'.$item->desc.'</p>' : '').
			($item->url && $item->desc ? '<a href="'.$item->url.'">Play video</a>' : '').
			'</td></tr>';
		}

		return $res;
	}

}

?>
