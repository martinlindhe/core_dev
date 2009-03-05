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
			if (call_user_func($this->handler, $_POST)) {
				//TODO: customize success message
				echo 'Form data processed successfully!<br/>';
				return;
			} else {
				//TODO: fill in form with previous entered data
				echo 'Failed to process form data!<br/>';
			}
		}

		echo xhtmlForm($this->name, '', 'post', $this->enctype);

		//TODO use xhtml_table class when it is created

		echo '<table cellpadding="10" cellspacing="0" border="1">';

		foreach ($this->elems as $e) {
			echo '<tr>';
			switch ($e['type']) {
			case 'INPUT':
				echo '<td>'.$e['str'].':</td>';
				echo '<td>'.xhtmlInput($e['name']).'</td>';
				break;

			case 'TEXTAREA':
				echo '<td>'.$e['str'].':</td>';
				echo '<td>'.xhtmlTextarea($e['name']).'</td>';
				break;

			case 'TEXT':
				echo '<td colspan="2">'.$e['str'].'</td>';
				break;

			case 'SUBMIT':
				echo '<td colspan="2">'.xhtmlSubmit($e['str']).'</td>';
				break;
			}
			echo '</tr>';
		}

		echo '</table>';

		echo xhtmlFormClose();
	}
}

?>
