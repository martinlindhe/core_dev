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
	var $enctype = '';     ///< TODO: set multipart type if form contains file upload parts
	var $handled = false;  ///< is set to true when form data has been processed by callback function
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
		$this->handled = false;

		if (!$func || !function_exists($func)) {
			die('FATAL: xhtml_form() does not have a defined data handler');
		}

		if (!empty($_POST)) {
			if (call_user_func($func, $_POST)) {
				//TODO: customize success message
				echo 'Form data processed successfully!<br/>';
				$this->handled = true;
				return;
			} else {
				//TODO: fill in form with previous entered data
				echo 'Failed to process form data!<br/>';
			}
		}
	}

	/**
	 * Adds a hidden input field to the form
	 */
	function hidden($name, $val)
	{
		$this->elems[] = array('type' => 'HIDDEN', 'name' => $name, 'value' => $val);
	}

	/**
	 * Adds a input field to the form
	 */
	function input($name, $str, $default = '')
	{
		$this->elems[] = array('type' => 'INPUT', 'name' => $name, 'str' => $str, 'default' => $default);
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
	 * Adds a select dropdown list to the form
	 */
	function dropdown($name, $str, $arr, $default = 0)
	{
		$this->elems[] = array('type' => 'DROPDOWN', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
	}

	function radio($name, $str, $arr, $default = 0)
	{
		$this->elems[] = array('type' => 'RADIO', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
	}

	/**
	 * Renders the form in XHTML
	 */
	function render()
	{
		if ($this->handled) return;

		echo xhtmlForm($this->name, '', 'post', $this->enctype);

		//TODO use xhtml_table class when it is created

		echo '<table cellpadding="10" cellspacing="0" border="1">';

		foreach ($this->elems as $e) {
			echo '<tr>';
			switch ($e['type']) {
			case 'HIDDEN':
				echo xhtmlHidden($e['name'], $e['value']);
				break;

			case 'INPUT':
				echo '<td>'.$e['str'].':</td>';
				echo '<td>'.xhtmlInput($e['name'], $e['default']).'</td>';
				break;

			case 'TEXTAREA':
				echo '<td>'.$e['str'].':</td>';
				echo '<td>'.xhtmlTextarea($e['name']).'</td>';
				break;

			case 'TEXT':
				echo '<td colspan="2">'.$e['str'].'</td>';
				break;

			case 'DROPDOWN':
				echo '<td>'.$e['str'].'</td>';
				echo '<td>'.xhtmlSelectArray($e['name'], $e['arr'], $e['default']).'</td>';
				break;

			case 'RADIO':
				echo '<td>'.$e['str'].'</td>';
				echo '<td>'.xhtmlRadioArray($e['name'], $e['arr'], $e['default']).'</td>';
				break;

			case 'SUBMIT':
				echo '<td colspan="2">'.xhtmlSubmit($e['str']).'</td>';
				break;

			default:
				echo '<h1>'.$e['type'].' not implemented</h1>';
				break;
			}
			echo '</tr>';
		}

		echo '</table>';

		echo xhtmlFormClose();
	}
}

?>
