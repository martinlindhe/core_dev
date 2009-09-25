<?php
/**
 * $Id$
 *
 * Simple feed (RSS, ATOM) and playlist (ASX, M3U) reader
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

require_once('client_http.php');

require_once('input_rss.php');
require_once('input_atom.php');

require_once('input_asx.php'); //XXX: FIXME use in io_playlist instead
require_once('input_m3u.php'); //XXX: FIXME use in io_playlist

//XXX TODO add input_xspf.php support

class input_feed
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
	private function parse($data, $callback = '')
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

?>
