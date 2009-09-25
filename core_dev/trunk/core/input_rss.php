<?php
/**
 * $Id$
 *
 * Reads and parses a rss feed
 *
 * http://en.wikipedia.org/wiki/Rss
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

class input_rss
{
	private $inside_item = false;
	private $current_tag = '';

	private $link, $title, $desc, $pubDate, $guid;
	//private $attrs;
	private $video_url, $video_type, $duration;
	private $image_url, $image_type;
	private $callback = '';
	private $guid2 = ''; //XXX remove this hack

	private $entries = array();

	function __construct($data, $callback = '')
	{
		if (is_url($data)) {
			$u = new http($data);
			$data = $u->fetch();
		}

		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startElement', 'endElement');
		xml_set_character_data_handler($parser, 'characterData');

		if (function_exists($callback)) $this->callback = $callback;

		if (!xml_parse($parser, $data, true)) {
			echo "parseRSS XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);
	}

	function getEntries() { return $this->entries; }

	function startElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;
		$this->attrs = $attrs;

		if ($this->current_tag == 'ITEM') {
			$this->inside_item = true;
		}
	}

	function endElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'ITEM') {
			$row['link']     = trim($this->link);
			$row['title']    = html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8');
			$row['desc']     = html_entity_decode(trim($this->desc),  ENT_QUOTES, 'UTF-8');
			if ($row['title'] == $row['desc']) $row['desc'] = '';
			$row['pubdate']  = strtotime(trim($this->pubDate));
			$row['guid']     = trim($this->guid);
			$row['guid2']    = trim($this->guid2); //XXX remove hack!
			$row['duration'] = trim($this->duration);
			if ($this->video_url)  $row['video'] = $this->video_url;
			if ($this->video_type) $row['video_type'] = $this->video_type;
			if ($this->image_url)  $row['image'] = $this->image_url;
			if ($this->video_type) $row['image_type'] = $this->image_type;

			if ($this->callback) call_user_func($this->callback, $row);
			$this->entries[] = $row;

			$this->attrs = '';
			$this->inside_item = false;

			$this->link = '';
			$this->title = '';
			$this->desc = '';
			$this->pubDate = '';
			$this->guid = '';
			$this->guid2 = ''; //XXX rmeove hack!
			$this->video_url  = '';
			$this->video_type = '';
			$this->duration   = 0;
			$this->image_url  = '';
			$this->image_type = '';
		}
	}

	function characterData($parser, $data)
	{
		if (!$this->inside_item) return;
		switch ($this->current_tag) {
		case 'LINK':
			$this->link .= $data;
			break;

		case 'TITLE':
			$this->title .= $data;
			break;

		case 'DESCRIPTION':
			$this->desc .= $data;
			break;

		case 'PUBDATE':
			$this->pubDate .= $data;
			break;

		case 'GUID':
			$this->guid .= $data;
			break;

		case 'SVTPLAY:XMLLINK':
			//XXX hack! fix callback mechanism to handle this
			$this->guid2 .= $data;
			break;

		case 'MEDIA:THUMBNAIL':
			if (!$this->image_url) { //XXX prefer full image over thumbnails
				$this->image_url  = $this->attrs['URL'];
				$this->image_type = 'image/jpeg';//$this->attrs['TYPE'];
			}
			break;

		case 'MEDIA:CONTENT':
			switch ($this->attrs['TYPE']) {
			case 'video/x-flv':
				if (substr($this->attrs['URL'],0,4) != 'rtmp' || !$this->video_url) { //XXX HACK: prefer asf (usually mms) over flv (usually over rtmp / rtmpe) because vlc dont support rtmp(e) so well yet (2009.09.23)
					$this->video_url  = $this->attrs['URL'];
					$this->video_type = $this->attrs['TYPE'];
					if (!empty($this->attrs['DURATION']))
						$this->duration = $this->attrs['DURATION'];
				}
				break;

			case 'video/x-ms-asf':
				if ($this->video_url && substr($this->attrs['URL'], -4) == '.asx') break; //skip .asx files if other was found
				$this->video_url  = $this->attrs['URL'];
				$this->video_type = $this->attrs['TYPE'];
				if (!empty($this->attrs['DURATION']))
					$this->duration = $this->attrs['DURATION'];
				break;

			case 'image/jpeg':
				$this->image_url  = $this->attrs['URL'];
				$this->image_type = $this->attrs['TYPE'];
				break;

			case 'text/html':
				//<media:content type="text/html" medium="document" url="http://svt.se/2.22620/1.1652031/krigsfartyg_soker_efter_arctic_sea">
				break;

			default:
				echo "unknown MEDIA:CONTENT: ".$this->attrs['TYPE']."\n";
				print_r($this->attrs);
				break;
			}
		}

	}

}

?>
