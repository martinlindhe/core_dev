<?php
/**
 * $Id$
 *
 * http://en.wikipedia.org/wiki/Advanced_Stream_Redirector
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: ok, needs more testing

/* FIXME test: only tried it with 1-entry files:
<asx version="3.0">
  <entry>
    <ref href="mms://wm0.c90901.cdn.qbrick.com/90901/kluster/20091021/PG-1133804-003A-BOOMSHAKALACK2-02.wmv"/>
    <author>svt.se</author>
    <copyright>Sveriges Television AB 2009</copyright>
  </entry>
</asx>
*/

require_once('client_http.php');

class input_asx
{
	private $entries = array();

	function __construct()
	{
	}

	/**
	 * @return array of objects
	 */
	function getItems() { return $this->entries; }

	/**
	 * Returns an ASX playlist parsed into a Playlist object
	 */
	function parse($data)
	{
		if (is_url($data)) {
			$u = new http($data);
			$u->setCacheTime(60 * 60); //1h
			$data = $u->get();

			//FIXME check http client return code for 404
			if (substr($data, 0, 5) != '<asx ') {
				dp('input_asx->parse FAIL: cant parse ASX feed from '.$u->getUrl() );
				return false;
			}
		}

		$reader = new XMLReader();
		$reader->xml($data);

		$item = new MediaItem();

		while ($reader->read())
		{
			if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'asx') {
				$this->entries[] = $item;
				$item = new MediaItem();
			}

			if ($reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch ($reader->name) {
			case 'asx':
				//d('version: '.$reader->getAttribute('version')); //XXX should be "3.0"
				break;

			case 'entry':
				while ($reader->read()) {
					if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == 'entry')
						break;

					if ($reader->nodeType != XMLReader::ELEMENT)
						continue;

					switch ($reader->name) {
					case 'author': break; //<author>svt.se</author>
					case 'copyright': break; //<copyright>Sveriges Television AB 2009</copyright>
					case 'starttime': break; //<starttime value="00:00:00.00"/>

					case 'ref': //<ref href="mms://wm0.c90901.cdn.qbrick.com/90901/kluster/20091026/aekonomi920.wmv"/>
						$item->url = $reader->getAttribute('href');
						break;

					case 'duration': //<duration value="00:03:39.00"/>
						$item->Duration->set( $reader->getAttribute('value') );
						break;

					default:
						echo "bad entry " .$reader->name.ln();
					}
				}
				break;
			default:
				echo "unknown ".$reader->name.ln();
				break;
			}
		}

		$reader->close();
		return true;
	}
}

?>
