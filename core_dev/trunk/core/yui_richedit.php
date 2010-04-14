<?php
/**
 * $Id$
 *
 * //http://developer.yahoo.com/yui/editor/
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//XXX: how to disable "insert image" button?
//XXX: how to change title from "Text Editing Tools" ?

class yui_richedit
{
	private $width = 400;
	private $height = 400;
	private $input_name = 'yui_richedit_input';

	function setWidth($n) { $this->width = $n; }
	function setHeight($n) { $this->height = $n; }
	function setInputName($s) { $this->input_name = $s; }

	function render()
	{
		$res =
		'<script type="text/javascript">'.
		//'var myEditor = new YAHOO.widget.Editor("'.$e['name'].'", {'.
		'var myEditor = new YAHOO.widget.SimpleEditor("'.$this->input_name.'", {'.
		'width: "'.$this->width.'px",'.
		'height: "'.$this->height.'px",'.
		'dompath: true,'. //Turns on the bar at the bottom
		'animate: true,'. //Animates the opening, closing and moving of Editor windows
		'handleSubmit: true,'. //editor will attach itself to the textareas parent form's submit handler
		'});'.
		'myEditor.render();'.
		'</script>';

		return $res;
	}
}

?>
