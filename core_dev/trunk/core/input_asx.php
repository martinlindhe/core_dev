<?php
/**
 * $Id$
 *
 * http://en.wikipedia.org/wiki/Advanced_Stream_Redirector
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: working, need cleanup
//TODO: rewrite to use XMLReader class

class input_asx
{
	private $inside_item = false;
	private $current_tag = '';

	private $link, $title;
	//private $attrs;
	private $callback = '';
	//private $guid2 = ''; //XXX remove this hack

	private $entries = array();

	function __construct($data, $callback = '')
	{
		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startElement', 'endElement');
		xml_set_character_data_handler($parser, 'characterData');

		if (function_exists($callback)) $this->callback = $callback;

		if (!xml_parse($parser, $data, true)) {
			echo "parseASX XML error: ".xml_error_string(xml_get_error_code($parser))." at line ".xml_get_current_line_number($parser)."\n";
		}
		xml_parser_free($parser);
	}

	function getEntries() { return $this->entries; }

	function startElement($parser, $name, $attrs = '')
	{
		$this->current_tag = $name;
		$this->attrs = $attrs;

		if ($this->current_tag == 'ENTRY') {
			$this->inside_item = true;
		}
	}

	function endElement($parser, $tagName, $attrs = '')
	{
		if ($tagName == 'ENTRY') {
			$row['link']  = trim($this->link);
			$row['title'] = html_entity_decode(trim($this->title), ENT_QUOTES, 'UTF-8');

			if ($this->callback) call_user_func($this->callback, $row);
			$this->entries[] = $row;

			$this->attrs = '';
			$this->inside_item = false;

			$this->link = '';
			$this->title = '';
		}
	}

	function characterData($parser, $data)
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

}

?>
