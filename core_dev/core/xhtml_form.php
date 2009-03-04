<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('output_xhtml.php');

class xhtml_form
{
	var $enctype = '';	//TODO: set multipart type if form contains file upload parts
	var $handler = '';
	var $name    = '';

	var $elems = array();

	function __construct($name = '')
	{
		$this->name = $name;
	}

	/**
	 * Defines the function that will handle form submit processing
	 */
	function handler($func)
	{
		$this->handler = $func;
	}

	/**
	 * Adds a input field to the form
	 */
	function input($name, $str)
	{
		$this->elems[] = array('type' => 'INPUT', 'name' => $name, 'str' => $str);
	}

	/**
	 * Adds a textarea to the form
	 */
	function textarea($name, $str)
	{
		$this->elems[] = array('type' => 'TEXTAREA', 'name' => $name, 'str' => $str);
	}

	/**
	 * Adds a text string to the form
	 */
	function text($str)
	{
		$this->elems[] = array('type' => 'TEXT', 'str' => $str);
	}

	/**
	 * Adds a submit button to the form
	 */
	function submit($str)
	{
		$this->elems[] = array('type' => 'SUBMIT', 'str' => $str);
	}

	/**
	 * Renders the form in XHTML
	 */
	function render()
	{
		if (!$this->handler || !function_exists($this->handler)) {
			die('FATAL: xhtml_form() does not have a defined data handler');
		}

		if (!empty($_POST)) {
			//XXX: avoid form processing if function return TRUE ?
			call_user_func($this->handler, $_POST);
		}

		echo xhtmlForm($this->name, '', 'post', $this->enctype);

		foreach ($this->elems as $e) {
			switch ($e['type']) {
				case 'INPUT':
					echo $e['str'].': ';
					echo xhtmlInput($e['name']).'<br/>';
					break;

				case 'TEXTAREA':
					echo $e['str'].': ';
					echo xhtmlTextarea($e['name']).'<br/>';
					break;

				case 'TEXT':
					echo $e['str'].'<br/>';
					break;

				case 'SUBMIT':
					echo xhtmlSubmit($e['str']);
					break;
			}
		}

		echo xhtmlFormClose();
	}
}

?>
