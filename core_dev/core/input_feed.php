<?php
/**
 * $Id$
 *
 * Simple RSS, ATOM and ASX feed/playlist parser
 * 
 * RSS: http://en.wikipedia.org/wiki/Rss
 * ATOM: http://en.wikipedia.org/wiki/Atom_(standard)
 * ASX: http://en.wikipedia.org/wiki/Advanced_Stream_Redirector
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//TODO: use input_xml for parsing

//TODO: extend callback to allow mapping of special fields to standard ones, such as "<svtplay:xmllink svtplay:type="titles">http://xml.svtplay.se/v1/titles/102897</svtplay:xmllink>" to "guid"

require_once('input_http.php'); //for is_url()

class input_feed
{
	var $inside_item = false;
	var $current_tag = '';
	
	var $link, $title, $desc, $pubDate, $guid;
	var $attrs;
	var $video_url, $video_type, $duration;
	var $image_url, $image_type;
	var $entries;
	var $callback = '';
	var $guid2 = ''; //XXX remove this hack

	var $sort = true;	///< sort output array?

	function RSSstartElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;
		$this->attrs = $attrs;

		if ($this->current_tag == 'ITEM') {
			$this->inside_item = true;
		}
	}

	function RSSendElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'ITEM') {
			$row['link']     = trim($this->link);
			$row['title']    = html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8');
			$row['desc']     = html_entity_decode(trim($this->desc),  ENT_QUOTES, 'UTF-8');
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

	function RSScharacterData($parser, $data)
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
				$this->video_url  = $this->attrs['URL'];
				$this->video_type = $this->attrs['TYPE'];
				$this->duration   = $this->attrs['DURATION'];
				break;

			case 'video/x-ms-asf':
				if (!$this->video_url) { //XXX prefer flv over asf
					$this->video_url  = $this->attrs['URL'];
					$this->video_type = $this->attrs['TYPE'];
					$this->duration   = $this->attrs['DURATION'];
				}
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


	function parseRSS($data, $callback = '')
	{
		if (is_url($data)) {
			$u = new url_handler($data);
			$data = $u->fetch();
		}

		$this->entries = array();

		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'RSSstartElement', 'RSSendElement');
		xml_set_character_data_handler($parser, 'RSScharacterData');

		if (function_exists($callback)) $this->callback = $callback;

		if (!xml_parse($parser, $data, true)) {
			echo "parseRSS XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);

		if ($this->sort) uasort($this->entries, 'feed_sort_desc');
		return $this->entries;
	}

	function ATOMstartElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;
		$this->attrs = $attrs;

		if ($this->current_tag == 'ENTRY') {
			$this->inside_item = true;
		}
	}

	function ATOMendElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'LINK' && $this->inside_item) {
			switch ($this->attrs['REL']) {
			case 'alternate':
				$this->link = $this->attrs['HREF'];
				break;
			case 'enclosure':
				switch ($this->attrs['TYPE']) {
				case 'video/x-flv':
					$this->video_url  = $this->attrs['URL'];
					$this->video_type = $this->attrs['TYPE'];
					$this->duration   = $this->attrs['LENGTH'];
					break;

				case 'image/jpeg':
					$this->image_url  = $this->attrs['URL'];
					$this->image_type = $this->attrs['TYPE'];
					break;

				default:
					echo "unknown enclosure mimetype: ".$this->attrs['TYPE']."\n";
					die;
					break;
				}
				break;
			default:
				echo "unknown link type: ".$this->attrs['REL']."\n";
				die;
			}
		}
		if ($tagName == 'ENTRY') {
			$row['link']     = trim($this->link);
			$row['title']    = html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8');
			$row['desc']     = html_entity_decode(trim($this->desc),  ENT_QUOTES, 'UTF-8');
			$row['pubdate']  = strtotime(trim($this->pubDate));
			$row['guid']     = trim($this->guid);
			//$row['guid2']    = trim($this->guid2); //XXX remove hack!
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
			//$this->guid2 = ''; //XXX rmeove hack!
			$this->video_url  = '';
			$this->video_type = '';
			$this->duration   = 0;
			$this->image_url  = '';
			$this->image_type = '';
		}
	}

	function ATOMcharacterData($parser, $data)
	{
		if (!$this->inside_item) return;
		switch ($this->current_tag) {
		case 'TITLE':
			$this->title .= $data;
			break;

		case 'SUMMARY':
			$this->desc .= $data;
			break;

		case 'UPDATED':
			$this->pubDate .= $data;
			break;

		case 'ID':
			$this->guid .= $data;
			break;
		}
	}

	function parseATOM($data, $callback = '')
	{
		if (is_url($data)) {
			$u = new url_handler($data);
			$data = $u->fetch();
		}

		$this->entries = array();

		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'ATOMstartElement', 'ATOMendElement');
		xml_set_character_data_handler($parser, 'ATOMcharacterData');

		if (function_exists($callback)) $this->callback = $callback;

		if (!xml_parse($parser, $data, true)) {
			echo "parseATOM XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);

		if ($this->sort) uasort($this->entries, 'feed_sort_desc');
		return $this->entries;
	}

	function ASXstartElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;
		$this->attrs = $attrs;

		if ($this->current_tag == 'ENTRY') {
			$this->inside_item = true;
		}
	}

	function ASXendElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'ENTRY') {
			$row['link']     = trim($this->link);
			$row['title']    = html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8');

			if ($this->callback) call_user_func($this->callback, $row);
			$this->entries[] = $row;

			$this->attrs = '';
			$this->inside_item = false;

			$this->link = '';
			$this->title = '';
		}
	}

	function ASXcharacterData($parser, $data)
	{
		if (!$this->inside_item) return;
		switch ($this->current_tag) {
		case 'REF':
			$this->link .= $this->attrs['HREF'];
			break;

		case 'TITLE':
			$this->title .= $data;
			break;

		case 'AUTHOR'://XXX save
			break;
			
		case 'COPYRIGHT': //XXX save
			break;
		}

	}
	
	function fetch($url, $callback = '')
	{
		if (!is_url($url)) return false;

		$u = new url_handler($url);
		$data = $u->get();

		if (strpos($data, '<rss ') !== false) {
			return $this->parseRSS($data, $callback);
		} else if (strpos($data, '<asx ') !== false) {
			return $this->parseASX($data, $callback);
		} else if (strpos($data, '<feed ') !== false) {
			return $this->parseATOM($data, $callback);
		}

		echo "ERROR: unhandled feed: ".substr($data, 0, 200)." ...\n";
		die;
	
	}
	

	function parseASX($data, $callback = '')
	{
		$this->entries = array();

		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'ASXstartElement', 'ASXendElement');
		xml_set_character_data_handler($parser, 'ASXcharacterData');

		if (function_exists($callback)) $this->callback = $callback;

		if (!xml_parse($parser, $data, true)) {
			echo "parseASX XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);

		if ($this->sort) uasort($this->entries, 'feed_sort_desc');
		return $this->entries;
	}
}

function feed_sort_desc($a, $b)
{
    return ($a['pubdate'] > $b['pubdate']) ? -1 : 1;
}

?>
