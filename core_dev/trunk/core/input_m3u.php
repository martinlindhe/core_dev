<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

class input_m3u
{
	private $entries = array();

	function __construct($data, $callback = '')
	{
		$this->entries = array();

		//if (function_exists($callback)) $this->callback = $callback;

		$rows = explode("\n", $data);
		foreach ($rows as $row) {
			$p = explode(':', $row, 2);
			switch ($p[0]) {
			case '#EXTM3U': case '': break;
			case '#EXTINF':
				$x = explode(',', $p[1], 2);
				$ent['length'] = ($x[0] != '-1' ? $x[0] : '');
				$ent['title']  = $x[1];
				break;

			default:
				$ent['link'] = $row;
				$this->entries[] = $ent;
				unset($ent);
				break;
			}
		}
	}

	function getEntries() { return $this->entries; }
}

?>
