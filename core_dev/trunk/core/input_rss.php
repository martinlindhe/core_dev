<?php
/**
 * $Id$
 *
 * Parses an RSS 2.0 feed into NewsItem objects
 *
 * http://www.rssboard.org/rss-specification
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: ok

require_once('class.CoreBase.php');
require_once('client_http.php');
require_once('io_newsfeed.php'); //for NewsItem object

class input_rss extends CoreBase
{
	private $items = array();   ///< list of NewsItem objects
	private $reader;            ///< XMLReader object
	private $title;             ///< title of the feed

	/**
	 * @return array of NewsItem objects
	 */
	function getItems() { return $this->items; }

	function getTitle() { return $this->title; }

	function parse($data)
	{
		if (is_url($data)) {
			$http = new HttpClient($data);
			if ($this->debug) $http->setDebug();
			$http->setCacheTime(60 * 60); //1h
			$data = $u->getBody();

			//FIXME check http client return code for 404
			if (strpos($data, '<rss ') === false) {
				dp('input_rss->parse FAIL: cant parse feed from '.$http->getUrl() );
				return false;
			}
		}
		if ($this->debug) echo 'Parsing RSS: '.$data.ln();

		$this->reader = new XMLReader();
		$this->reader->xml($data);

		while ($this->reader->read())
		{
			if ($this->reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch ($this->reader->name) {
			case 'rss':
				if ($this->reader->getAttribute('version') != '2.0')
					die('XXX FIXME unsupported RSS version '.$this->reader->getAttribute('version') );
				break;

			case 'channel':
				$this->parseChannel();
				break;

			default:
				echo 'bad top entry '.$this->reader->name.ln();
				break;
			}
		}

		$this->reader->close();
		return true;
	}

	private function parseChannel()
	{
		while ($this->reader->read()) {
			if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'channel')
				return;

			if ($this->reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch (strtolower($this->reader->name)) {
			case 'title':
				$this->reader->read();
				$this->title = $this->reader->value;
				break;

			case 'link': break;
			case 'description': break;
			case 'language': break;
			case 'pubdate': break;
			case 'generator': break;
			case 'webmaster': break;
			//case 'lastbuilddate': break; //<lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>
			//case 'docs': break; //<docs>http://blogs.law.harvard.edu/tech/rss</docs>
			//case 'managingeditor': break; //<managingEditor>editor@example.com</managingEditor>

			case 'item':
				$this->parseItem();
				break;

			default:
				//echo 'unknown channel entry ' .$this->reader->name.ln();
				break;
			}
		}
	}

	private function parseItem()
	{
		$item = new NewsItem();

		while ($this->reader->read()) {
			if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'item') {
				if ($item->title == $item->desc) $item->desc = '';
				$this->items[] = $item;
				return;
			}

			if ($this->reader->nodeType != XMLReader::ELEMENT)
				continue;

			switch (strtolower($this->reader->name)) {
			case 'title':
				$this->reader->read();
				$item->title = trim( html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8') );
				break;

			case 'description':
				$this->reader->read();
				$item->desc = trim( html_entity_decode($this->reader->value, ENT_QUOTES, 'UTF-8') );
				break;

			case 'author':
				$this->reader->read();
				$item->author = $this->reader->value;
				break;

			case 'link':
				$this->reader->read();
				$item->Url->set( $this->reader->value );
				break;

			case 'pubdate':
				$this->reader->read();
				$item->Timestamp->set( $this->reader->value );
				break;

			case 'guid':
				$this->reader->read();
				$item->guid = $this->reader->value;
				break;

			case 'media:thumbnail':
				if (!$item->image_url) { //XXX prefer full image over thumbnails
					$item->image_url  = $this->reader->getAttribute('url');
					$item->image_mime = file_get_mime_by_suffix($item->image_url);//$this->reader->getAttribute('type')
				}
				break;

			case 'media:content':
				switch ($this->reader->getAttribute('type')) {
				case 'video/x-flv':
					//XXX HACK: prefer asf (usually mms) over flv (usually over rtmp / rtmpe) because vlc dont support rtmp(e) so well yet (2009.09.23)
					if (substr($this->reader->getAttribute('url'),0,4) != 'rtmp' || !$item->video_url) {
						$item->video_url  = $this->reader->getAttribute('url');
						$item->video_mime = $this->reader->getAttribute('type');

						$item->Duration->set($this->reader->getAttribute('duration'));
					}
					break;

				case 'video/x-ms-asf':
					if (file_suffix($this->reader->getAttribute('url')) == '.asx')
					{
						$asx = new input_asx();
						if ($this->debug) $asx->setDebug();
						$asx->parse( $this->reader->getAttribute('url') );
						$list = $asx->getItems();

						if ($list)
							$item->video_url = $list[0]->Url->get();
					} else {
						$item->video_url = $this->reader->getAttribute('url');
					}

					$item->video_mime  = $this->reader->getAttribute('type');
					$item->Duration->set($this->reader->getAttribute('duration'));
					break;

				case 'video/quicktime':
					$item->video_url  = $this->reader->getAttribute('url');
					$item->video_mime = $this->reader->getAttribute('type');
					$item->Duration->set($this->reader->getAttribute('duration'));
					break;

				case 'image/jpeg':
					$item->image_url  = $this->reader->getAttribute('url');
					$item->image_mime = $this->reader->getAttribute('type');
					break;

				case 'text/html':
					//<media:content type="text/html" medium="document" url="http://svt.se/2.22620/1.1652031/krigsfartyg_soker_efter_arctic_sea">
					break;

				default:
					echo 'input_rss->parseItem() unknown MEDIA:CONTENT: '.$this->reader->getAttribute('type')."\n";
					break;
				}
				break;

			default:
				//echo 'unknown item entry ' .$this->reader->name.ln();
				break;
			}
		}
	}

}

?>
