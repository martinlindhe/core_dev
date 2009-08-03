<?php
/**
 * $Id$
 *
 * Simple RSS parser
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//TODO: identify and handle atom feeds transparently

class rss_input
{
	var $inside_item = false;
	var $current_tag = '';
	var $link, $title, $desc, $pubDate;
	var $attrs;
	var $video_url, $video_type, $duration;
	var $image_url, $image_type;
	var $entries = array();
	var $callback = '';

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
			$row['pubdate']  = strtotime(trim($this->pubDate));
			$row['duration'] = $this->duration;
			if ($this->video_url) $row['video'] = $this->video_url;
			if ($this->image_url) $row['image'] = $this->image_url;

			$this->entries[] = $row;
			if ($this->callback) call_user_func($this->callback, $row);

			$this->link = '';
			$this->title = '';
			$this->desc = '';
			$this->pubDate = '';
			$this->attrs = '';
			$this->video_url  = '';
			$this->video_type = '';
			$this->duration   = 0;
			$this->image_url  = '';
			$this->image_type = '';
			$this->inside_item = false;
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

			case 'MEDIA:CONTENT':
				switch ($this->attrs['TYPE']) {
				case 'video/x-flv':
					$this->video_url  = $this->attrs['URL'];
					$this->video_type = $this->attrs['TYPE'];
					$this->duration   = $this->attrs['DURATION'];
					break;

				case 'image/jpeg':
					$this->image_url  = $this->attrs['URL'];
					$this->image_type = $this->attrs['TYPE'];
					break;

				default:
					echo "unknown MEDIA:CONTENT: ".$this->attrs['TYPE']."\n";
					print_r($this->attrs);
				}
				break;
		}

	}

	function parse($data, $callback = '')
	{
		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startElement', 'endElement');
		xml_set_character_data_handler($parser, 'characterData');

		if (function_exists($callback)) $this->callback = $callback;

		if (!xml_parse($parser, $data, true)) {
			echo "input_rss XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);

		return $this->entries;
	}
}

?>
