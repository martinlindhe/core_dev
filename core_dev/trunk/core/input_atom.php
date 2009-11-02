<?php
/**
 * $Id$
 *
 * Parses an Atom 1.0 feed into NewsItem objects
 *
 * http://en.wikipedia.org/wiki/Atom_(standard)
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: ok, need testing

require_once('client_http.php');

class input_atom
{
	private $entries = array();

	private $reader; ///< XMLReader object

	/**
	 * @return array of NewsItem objects
	 */
	function getItems() { return $this->entries; }

	function parse($data)
	{
		if (is_url($data)) {
			$u = new HttpClient($data);
			$u->setCacheTime(60 * 60); //1h
			$data = $u->getBody();

			//FIXME check http client return code for 404
			if (strpos($data, '<feed ') === false) {
				dp('input_atom->parse FAIL: cant parse feed from '.$u->getUrl() );
				return false;
			}
		}

		$this->reader = new XMLReader();
		$this->reader->xml($data);

		while ($this->reader->read())
		{
			if ($this->reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch ($this->reader->name) {
			case 'feed':
				if ($this->reader->getAttribute('xmlns') != 'http://www.w3.org/2005/Atom')
					die('error unknown atom xmlns: '.$this->reader->getAttribute('xmlns') );
				break;

			case 'entry':
				$this->parseEntry();
				break;

			case 'id': break;
			case 'title': break;
			case 'link': break;
			case 'generator': break;

			default:
				echo 'bad top entry '.$this->reader->name.ln();
				break;
			}
		}

		$this->reader->close();
		return true;
	}

	private function parseEntry()
	{
		$item = new NewsItem();

		while ($this->reader->read()) {
			if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'entry') {
				if ($item->title == $item->desc) $item->desc = '';
				$this->entries[] = $item;
				$item = new NewsItem();
				break;
			}

			if ($this->reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch (strtolower($this->reader->name)) {
			case 'title':
				$this->reader->read();
				$item->title = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
				break;

			case 'summary':
				$this->reader->read();
				$item->desc = html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8');
				break;

			case 'updated':
				$this->reader->read();
				$item->Timestamp->set( $this->reader->value );
				break;

			case 'id':
				$this->reader->read();
				$item->guid = $this->reader->value;
				break;

			case 'link':
				switch ($this->reader->getAttribute('rel')) {
				case 'alternate':
					$item->url = $this->reader->getAttribute('href');
					break;
				case 'enclosure':
					switch ($this->reader->getAttribute('type')) {
					case 'video/x-flv':
					case 'video/quicktime':
						$item->video_url  = $this->reader->getAttribute('href');
						$item->video_mime = $this->reader->getAttribute('type');
						if ($this->reader->getAttribute('length')) $this->duration = $this->reader->getAttribute('length');
						break;

					case 'image/jpeg':
						$item->image_url  = $this->reader->getAttribute('href');
						$item->image_mime = $this->reader->getAttribute('type');
						break;

					default:
						die('input_atom->parseLink() unknown enclosure mime: '.$this->reader->getAttribute('type') );
					}
					break;

				case 'image':
					switch ($this->reader->getAttribute('type')) {
					case 'image/png':
						$item->image_url  = $this->reader->getAttribute('href');
						$item->image_mime = $this->reader->getAttribute('type');
						break;

					default:
						die('input_atom->parseLink() unknown image mime: '.$this->reader->getAttribute('type') );
					}
					break;

				case 'replies':
					//FIXME: handle
					break;
				case 'edit': //XXX ???
				case 'self': //XXX ???
					break;
				default:
					die('input_atom->parseLink() unknown link type: '.$this->reader->getAttribute('rel') );
				}
				break;

			default:
				//echo 'unknown entry entry '.$this->reader->name.ln();
				break;
			}
		}
	}

	private function parseLink()
	{

	}

}

?>
