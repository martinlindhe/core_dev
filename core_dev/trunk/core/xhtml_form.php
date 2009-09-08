<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('output_xhtml.php');

//TODO render() use xhtml_table class when it is created
//TODO add yui interactive editor to superTextarea field

class xhtml_form
{
	private $enctype = '';     ///< TODO: set multipart type if form contains file upload parts
	private $handled = false;  ///< is set to true when form data has been processed by callback function
	private $name    = '';

	private $handler;
	private $formData = array();

	private $listenGet = false; ///< if true, looks for form parameters in _GET

	private $elems = array();
	private $yui   = false;    ///< include yui js files?

	private $success = 'Form data processed successfully!';
	private $error   = 'Submitted form was rejected!';

	function __construct($name = '')
	{
		$this->name = $name;
	}

	function setError($s) { $this->error = $s; }
	function setSuccess($s) { $this->success = $s; }

	/**
	 * Defines the function that will handle form submit processing
	 * Call this function when all form elements have been added in order to fetch
	 * GET/POST parameters from previous page view
	 *
	 * @param $f function name to process form data
	 */
	function setHandler($f) {
		$this->handler = $f;

		$this->handle();
	}

	/**
	 * Processes the form
	 */
	function handle()
	{
		$p = array();

		if (!empty($_POST))
			$p = $_POST;

		if ($this->listenGet && !empty($_GET))
			foreach ($_GET as $key => $val)
				foreach ($this->elems as $row)
					if (!empty($row['name']) && $row['name'] == $key)
						$p[ $key ] = $val;

		if (!$p) return;

		//find new_catname tag and handle it
		foreach ($this->elems as $id => $e) {
			if (!empty($e['name']) && !empty($p['new_'.$e['name']])) {
				//add category
				$id = $this->elems[ $id ]['obj']->add($p['new_'.$e['name']]);

				//modify post form category id, unset new_catname
				$p[ $e['name'] ] = $id;
				unset( $p['new_'.$e['name']] );
			}
		}

		$this->formData = $p;

		if (call_user_func($this->handler, $this->formData, $this)) {
			$this->handled = true;
			echo '<div class="okay">'.$this->success.'</div><br/>';
			return;
		} else {
			echo '<div class="critical">'.$this->error.'</div><br/>';
		}
	}

	function setListenGet($bool) { $this->listenGet = $bool; }

	/**
	 * Adds a hidden input field to the form
	 */
	function addHidden($name, $val)
	{
		$this->elems[] = array('type' => 'HIDDEN', 'name' => $name, 'value' => $val);
	}

	/**
	 * Adds a input field to the form
	 */
	function addInput($name, $str, $val = '', $size = 0)
	{
		$this->elems[] = array('type' => 'INPUT', 'name' => $name, 'str' => $str, 'default' => $val, 'size' => $size);
	}

	/**
	 * Adds a textarea to the form
	 */
	function addTextarea($name, $str, $val = '')
	{
		$this->elems[] = array('type' => 'TEXTAREA', 'name' => $name, 'str' => $str, 'default' => $val);
	}

	/**
	 * Adds a text string to the form
	 */
	function addText($str)
	{
		$this->elems[] = array('type' => 'TEXT', 'str' => $str);
	}

	/**
	 * Adds a submit button to the form
	 */
	function addSubmit($str)
	{
		$this->elems[] = array('type' => 'SUBMIT', 'str' => $str);
	}

	/**
	 * Adds a select dropdown list to the form
	 * @param $arr array with id=>name pairs
	 */
	function addDropdown($name, $str, $arr, $default = 0)
	{
		$this->elems[] = array('type' => 'DROPDOWN', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
	}

	function addRadio($name, $str, $arr, $default = 0)
	{
		$this->elems[] = array('type' => 'RADIO', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
	}

	/**
	 * Adds a category object to the form
	 * @param $obj category object
	 */
	function addCategory($name, $str, $obj, $default = 0)
	{
		$this->elems[] = array('type' => 'CATEGORY', 'name' => $name, 'str' => $str, 'obj' => $obj, 'default' => $default);
	}

	/**
	 * Adds a calendar date selector
	 */
	function addDateInterval($namefrom, $nameto, $str)
	{
		$this->yui = true;
		$this->elems[] = array('type' => 'DATEINTERVAL', 'namefrom' => $namefrom, 'nameto' => $nameto, 'str' => $str);
	}

	/**
	 * Renders the form in XHTML
	 */
	function render()
	{
		global $config;

		if (!function_exists($this->handler))
			die('FATAL: xhtml_form() does not have a defined data handler');

		if ($this->yui) {
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/yui/yahoo-dom-event/yahoo-dom-event.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/yui/calendar/calendar-min.js"></script>';

			echo '<link type="text/css" rel="stylesheet" href="'.$config['core']['web_root'].'js/yui/calendar/assets/skins/sam/calendar.css">';
		}

		echo xhtmlForm($this->name, '', 'post', $this->enctype);

		echo '<table cellpadding="10" cellspacing="0" border="1">';

		foreach ($this->elems as $e)
		{
			//fills in form with previous entered data
			if (!empty($e['name']) && !empty($this->formData[$e['name']]))
				$e['default'] = $this->formData[$e['name']];

			echo '<tr>';
			switch ($e['type']) {
			case 'HIDDEN':
				echo xhtmlHidden($e['name'], $e['value']);
				break;

			case 'INPUT':
				if ($e['str']) {
					echo '<td>'.$e['str'].'</td>';
					echo '<td>'.xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
				} else {
					echo '<td colspan="2">'.xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
				}
				break;

			case 'TEXTAREA':
				echo '<td>'.$e['str'].'</td>';
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

			case 'CATEGORY':
				echo '<td>'.$e['str'].'</td>';
				echo '<td>';
				$list = $e['obj']->getList();
				echo xhtmlSelectArray($e['name'], $list, $e['default']).' ';
				//add new category widget
				echo '<a href="#" onClick="toggle_element(\'cd_new_'.$e['name'].'\');toggle_enabled_element(\'new_'.$e['name'].'\');">'.coreButton('Add').'</a>';
				echo '<span id="cd_new_'.$e['name'].'" style="display:none;">';
				echo xhtmlInput('new_'.$e['name'], 'new category', 15, 0, true);
				echo '</span>';
				echo '</td>';
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
