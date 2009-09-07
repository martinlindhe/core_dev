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

require_once('output_list.php');

class output_playlist extends core_list
{
	private $sendHeaders = false; ///< shall we send mime type?

	function enableHeaders() { $this->sendHeaders = true; }
	function disableHeaders() { $this->sendHeaders = false; }

	function render($format = 'xspf')
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
			return $this->renderHTML();
		}

		echo "output_playlist: unknown format ".$format."\n";
		return false;
	}

	function renderXSPF()
	{
		$res  = '<?xml version="1.0" encoding="UTF-8"?>';
		$res .= '<playlist version="1" xmlns="http://xspf.org/ns/0/">';
		$res .= '<trackList>'."\n";

		foreach ($this->getEntries() as $row) {
			//XXX: xspf spec dont have a way to add a timestamp for each entry (??)
			//XXX: create categories from $row['category']

			$vid_url = new http($row['video']);
			$img_url = new http($row['image']);

			$res .= '<track>';
			$title = formatTime($row['pubdate']).' '.$row['title'];
			//if ($row['desc']) $title .= ' - '.$row['desc'];
			$res .= '<title><![CDATA['.trim($title).']]></title>';

			$res .= '<location>'.$vid_url->render().'</location>';

			if (!empty($row['duration']))
				$res .= '<duration>'.($row['duration']*1000).'</duration>'; //in milliseconds

			if (!empty($row['image'])) {
				$res .= '<image>'.$img_url->render().'</image>';
			}

			$res .= '</track>'."\n";
		}

		$res .= '</trackList>';
		$res .= '</playlist>';

		return $res;
	}

	function renderM3U()
	{
		$res = "#EXTM3U\n";
		foreach ($this->getEntries() as $row) {
			$res .= "#EXTINF:".(!empty($row['duration']) ? round($row['duration'], 0) : '-1').",".$row['title']."\n";
			$res .= $row['video']."\n";
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
		foreach ($this->getEntries() as $row) {
			$i++;
			$res .= "File".$i."=".$row['video']."\n";
			$res .= "Title".$i."=".$row['title']."\n";
			$res .= "Length".$i."=".(!empty($row['duration']) ? $row['duration'] : '-1')."\n\n";
		}
		$res .= "Version=2\n";
		return $res;
	}

	/**
	 * Renders the playlist as a HTML table
	 */
	function renderHTML()
	{
		$res = '<table border="1">';

		foreach ($this->getEntries() as $row) {
			$res .= '<tr><td>';
			$res .= '<h2>'.formatTime($row['pubdate']).' '.(!empty($row['link']) ? '<a href="'.$row['link'].'">' : '').$row['title'].(!empty($row['link']) ? '</a>' : '').'</h2>';
			$res .= '<img src="'.$row['image'].'" width="320" style="float: left; padding: 10px;"/>';
			$res .= '<p>'.$row['desc'].'</p>';
			if (!empty($row['video'])) $res .= '<a href="'.$row['video'].'">Play video</a>';
			$res .= '</td></tr>';
		}

		return $res;
	}


}

?>
