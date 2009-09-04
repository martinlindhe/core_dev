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
	private $enctype = '';     ///< TODO: set multipart type if form contains file upload parts
	private $handled = false;  ///< is set to true when form data has been processed by callback function
	private $name    = '';

	private $handler;
	private $failPOST = array();

	private $elems = array();
	private $yui   = false;    ///< include yui js files?

	function __construct($name = '')
	{
		$this->name = $name;
	}

	/**
	 * Defines the function that will handle form submit processing
	 */
	function setHandler($f) {
		$this->handler = $f;

		if (!empty($_POST)) {

			if (call_user_func($this->handler, $_POST)) {
				//TODO: customize success message
				echo 'Form data processed successfully!<br/>';
				$this->handled = true;
				return;
			} else {
				$this->failPOST = $_POST;
				echo 'Submitted form was rejected<br/>';
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
	function input($name, $str, $val = '', $size = 0)
	{
		$this->elems[] = array('type' => 'INPUT', 'name' => $name, 'str' => $str, 'default' => $val, 'size' => $size);
	}

	/**
	 * Adds a textarea to the form
	 */
	function textarea($name, $str, $val = '')
	{
		$this->elems[] = array('type' => 'TEXTAREA', 'name' => $name, 'str' => $str, 'default' => $val);
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
	 * Adds a calendar date selector
	 */
	function dateinterval($namefrom, $nameto, $str)
	{
		$this->yui = true;
		$this->elems[] = array('type' => 'DATEINTERVAL', 'namefrom' => $namefrom, 'nameto' => $nameto, 'str' => $str);
	}

	/**
	 * Renders the form in XHTML
	 */
	function render()
	{
		if (!function_exists($this->handler)) {
			die('FATAL: xhtml_form() does not have a defined data handler');
		}

		global $config;
		if ($this->handled) return;

		if ($this->yui) {
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/yui/yahoo-dom-event/yahoo-dom-event.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/yui/calendar/calendar-min.js"></script>';

			echo '<link type="text/css" rel="stylesheet" href="'.$config['core']['web_root'].'js/yui/calendar/assets/skins/sam/calendar.css">';
		}

		echo xhtmlForm($this->name, '', 'post', $this->enctype);

		//TODO use xhtml_table class when it is created

		echo '<table cellpadding="10" cellspacing="0" border="1">';

		foreach ($this->elems as $e)
		{
			//fills in form with previous entered data
			if (!empty($e['name']) && !empty($this->failPOST[$e['name']]))
				$e['default'] = $this->failPOST[$e['name']];

			echo '<tr>';
			switch ($e['type']) {
			case 'HIDDEN':
				echo xhtmlHidden($e['name'], $e['value']);
				break;

			case 'INPUT':
				if ($e['str']) {
					echo '<td>'.$e['str'].':</td>';
					echo '<td>'.xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
				} else {
					echo '<td colspan="2">'.xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
				}
				break;

			case 'TEXTAREA':
				echo '<td>'.$e['str'].':</td>';
				echo '<td>'.xhtmlTextarea($e['name'], $e['default']).'</td>';
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

			case 'DATEINTERVAL':
				echo '<td colspan="2">';

				echo '<div id="cal1Container"></div>';
				echo '<div style="clear:both"></div>';

				echo xhtmlInput($e['namefrom']).' - '.xhtmlInput($e['nameto']).'<br/>';

				require_once('js_calendar.js');
				echo '</td>';
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
