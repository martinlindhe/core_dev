<?php
/**
 * $Id$
 *
 * Simple RSS parser
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

class rss_input
{
	var $inside_item = false;
	var $current_tag = '';
	var $link, $title, $desc, $pubDate;
	var $entries = array();
	var $callback = '';

	function startElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;

		if ($this->current_tag == 'ITEM') $this->inside_item = true;
	}

	function endElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'ITEM') {
			$row = array(
				'link' => trim($this->link),
				'title' => html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8'),
				'desc' => html_entity_decode(trim($this->desc), ENT_QUOTES, 'UTF-8'),
				'pubdate' => strtotime(trim($this->pubDate))
			);
			$this->entries[] = $row;
			if ($this->callback) call_user_func($this->callback, $row);

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
