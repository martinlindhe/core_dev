<?php
/**
 * $Id$
 *
 * XML reading helper
 */

//TODO: use this class in many more places

class xml_input
{
    var $name;
    var $attr;
    var $data  = array();
    var $stack = array();
    var $keys;
    var $path;

    function parse($xml)
    {
		$parser = xml_parser_create ("UTF-8");
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startXML', 'endXML');
		xml_set_character_data_handler($parser, 'charXML');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

		if (!xml_parse($parser, $xml)) {
			sprintf('XML error at line %d column %d',
				xml_get_current_line_number($parser),
				xml_get_current_column_number($parser));
		}
		return $this->data;
    }

	function startXML($parser, $name, $attr)
	{
		$this->stack[$name] = array();
		$keys = '';
		$total = count($this->stack)-1;
		$i=0;
		foreach ($this->stack as $key => $val) {
			if (count($this->stack) > 1 && $i !=$total)
				$keys .= $key.'|';
			else
				$keys .= $key;
			$i++;
		}
		/*
		if (array_key_exists($keys, $this->data))
			$this->data[$keys][] = $attr;
		else
			$this->data[$keys] = $attr;
		*/
		$this->keys = $keys;
	}

	function endXML($parser, $name)
	{
		end($this->stack);
		if (key($this->stack) == $name) array_pop($this->stack);
	}

	function charXML($parser, $data)
	{
		$data = trim($data);
		if (empty($data)) return;

		$this->data[$this->keys] = $data;
	}
}

?>
