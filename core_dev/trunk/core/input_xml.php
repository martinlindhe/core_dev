<?php
/**
 * $Id$
 *
 * XML reading helper
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//TODO: use this class in many more places

require_once('client_http.php');

class xml_input
{
    var $name;
    var $attr;
    var $data;
    var $stack;
    var $keys;
    var $path;
	var $index, $idxval, $value;
	var $cache_time = 0;

	/**
	 * @param $index if set, name of field to read index from
	 * @param $value if set, name of field to read value from
	 */
    function parse($data, $index = '', $value = '')
    {
		if (is_url($data)) {
			$u = new http($data);
			$u->cache_time = $this->cache_time;
			$data = $u->get();
		}

		$this->name = '';
		$this->attr = '';
		$this->data = array();
		$this->stack = array();
		$this->keys = '';
		$this->path = '';
		$this->index = $index;
		$this->idxval = '';
		$this->value = $value;

		$parser = xml_parser_create('UTF-8');
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startXML', 'endXML');
		xml_set_character_data_handler($parser, 'charXML');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

		if (!xml_parse($parser, $data)) {
			sprintf('XML error at line %d column %d',
				xml_get_current_line_number($parser),
				xml_get_current_column_number($parser));
		}
		return $this->data;
    }

	function startXML($parser, $name, $attr)
	{
		$this->name = $name;
		if ($this->index) return;

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

		if (!empty($attr))
			$this->data[$keys][] = $attr;

/*
array_key_exists
		else if (!empty($attr)) {
			echo "key: ".$keys."\n";

			print_r($attr);
			die('special '.$attr);
			* */
		//}


		$this->keys = $keys;
	}

	function endXML($parser, $name)
	{
		if ($this->index) return;

		end($this->stack);
		if (key($this->stack) == $name) array_pop($this->stack);
	}

	function charXML($parser, $data)
	{
		$data = trim($data);
		if (empty($data)) return;

		if ($this->index == $this->name)
			$this->idxval = $data;
		else if ($this->value == $this->name)
			$this->data[$this->idxval] = $data;
		else
			$this->data[$this->keys] = $data;
	}
}

?>
