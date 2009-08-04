<?php
/**
 * Generates a VLC-compilant XSPF playlist
 *
 * http://validator.xspf.org/
 * http://en.wikipedia.org/wiki/Xspf
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('functions_defaults.php');

class xspf
{
	function render($items)
	{
		//XXX totem cant load xspf from network, only local disk! test new version
		//vlc plays this correctly
		$res  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$res .= '<playlist version="1" xmlns="http://xspf.org/ns/0/">'."\n";
		$res .= "\t<trackList>\n";

		foreach ($items as $row) {
			//XXX: xspf spec dont have a way to add a timestamp for each entry (??)
			//XXX: create categories from $row['category']
			$res .= "\t\t<track>\n";
			$title = formatTime($row['pubdate']).' '.$row['title'];
			//if ($row['desc']) $title .= ' - '.$row['desc'];
			$res .= "\t\t\t<title><![CDATA[".$title."]]></title>\n";

			$res .= "\t\t\t<location>".$row['video']."</location>\n";

			if (!empty($row['duration']))
				$res .= "\t\t\t<duration>".($row['duration']*1000)."</duration>\n"; //in milliseconds

			if (!empty($row['image'])) {
				$res .= "\t\t\t<image>".$row['image']."</image>\n";
			}

			$res .= "\t\t</track>\n";
		}

		$res .= "\t</trackList>\n";
		$res .= "</playlist>\n";

		return $res;
	}
}

?>
