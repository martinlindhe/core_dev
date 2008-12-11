<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

class rss_input
{
	var $inside_item;
	var $current_tag;
	var $link, $title, $desc, $pubDate;
	var $entries = array();

	function startElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;

		if ($this->current_tag == 'ITEM') $this->inside_item = true;
	}

	function endElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'ITEM') {
			$this->entries[] = array(
				'link' => trim($this->link),
				'title' => trim($this->title),
				'desc' => trim($this->desc),
				'pubdate' => strtotime(trim($this->pubDate))
			);

			$this->link = '';
			$this->title = '';
			$this->desc = '';
			$this->pubDate = '';
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
		}
	}

	function parse($data)
	{
		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startElement', 'endElement');
		xml_set_character_data_handler($parser, 'characterData');

		if (!xml_parse($parser, $data, true)) {
			echo "input_rss XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);

		return $this->entries;
	}

}


?>
