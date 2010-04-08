<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('client_captcha.php');
require_once('output_xhtml.php');

//FIXME: include yui resources in a smarter way

class xhtml_form
{
	private $enctype = '';     ///< TODO: set multipart type if form contains file upload parts
	private $handled = false;  ///< is set to true when form data has been processed by callback function
	private $name    = '';

	private $handler;
	private $objectinstance = false;

	private $formData = array();

	private $listenGet = false;

	private $elems = array();
	private $yui_dateinterval = false;    ///< include yui files for date interval picker?
	private $yui_richedit     = false;    ///< include yui files for richedit?

	private $success = '';
	private $error   = 'Submitted form was rejected!';

	function __construct($name = '')
	{
		$this->name = $name;
	}

	function setError($s) { $this->error = $s; }

	function setSuccess($s) { $this->success = $s; }

	/**
	 * $param $bool set to true to look for form parameters in _GET if not found in _POST
	 */
	function setListenGet($bool) { $this->listenGet = $bool; }

	/**
	 * Defines the function/object->method that will handle form submit processing
	 * Call this function when all form elements have been added in order
	 * to fetch GET/POST parameters from previous page view
	 *
	 * @param $f function/method name to process form data
	 * @param $objectinstance for objects
	 */
	function setHandler($f, $objectinstance = false)
	{
		$this->handler = $f;

		if (is_object($objectinstance))
			$this->objectinstance = $objectinstance;

		$this->handle();
	}

	/**
	 * Processes the form
	 */
	function handle()
	{
		global $h;

		$p = array();

		if (!empty($_POST))
			foreach ($_POST as $key => $val)
				foreach ($this->elems as $row) {
					if (   !empty($row['name'])     && $row['name'] == $key
						|| !empty($row['namefrom']) && $row['namefrom'] == $key
						|| !empty($row['nameto'])   && $row['nameto'] == $key
					)
						$p[ $key ] = $val;
				}

		//catch named GET parameters if no POST parameters are found
		if ($this->listenGet && !empty($_GET))
			foreach ($_GET as $key => $val)
				foreach ($this->elems as $row)
					if (!empty($row['name']) && !isset($_POST[$row['name']]) && $row['name'] == $key)
						$p[ $key ] = $val;

		if (!$p) return false;

		//find new_catname tag and handle it
		foreach ($this->elems as $id => $e) {
			if (!empty($e['name']) && !empty($p['new_'.$e['name']])) {
				//add category
				$cat = new CategoryItem($this->elems[$id]['cat_type']);
				$cat->setOwner($h->session->id);
				$cat->setTitle($p['new_'.$e['name']]);
				$id = $cat->store();

				//modify post form category id, unset new_catname
				$p[ $e['name'] ] = $id;
				unset( $p['new_'.$e['name']] );
			}
		}

		$this->formData = $p;

		if ($this->objectinstance) {
			$call = array($this->objectinstance, $this->handler);
		} else {
			$call = $this->handler;
		}

		if (call_user_func($call, $this->formData, $this)) {
			$this->handled = true;
		}

		if ($this->handled) {
			if ($this->success) echo '<div class="okay">'.$this->success.'</div><br/>';
			return true;
		}
		echo '<div class="critical">'.$this->error.'</div><br/>';
		return false;
	}

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
	 * Adds a checkbox field to the form
	 */
	function addCheckbox($name, $str, $val = '1', $checked = false)
	{
		$this->elems[] = array('type' => 'CHECKBOX', 'name' => $name, 'str' => $str, 'default' => $val, 'checked' => $checked);
	}

	/**
	 * Adds a textarea to the form
	 */
	function addTextarea($name, $str, $val = '', $width = 0, $height = 0)
	{
		$this->elems[] = array('type' => 'TEXTAREA', 'name' => $name, 'str' => $str, 'default' => $val, 'width' => $width, 'height' => $height);
	}

	/**
	 * Adds a richedit textarea to the form
	 */
	function addRichedit($name, $str, $val = '', $width = 500, $height = 200)
	{
		$this->yui_richedit = true;
		$this->elems[] = array('type' => 'RICHEDIT', 'name' => $name, 'str' => $str, 'default' => $val, 'width' => $width, 'height' => $height);
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
	 * Adds a category to the form
	 * @param $cat_type category type
	 */
	function addCategory($name, $str, $cat_type, $default = 0)
	{
		$this->elems[] = array('type' => 'CATEGORY', 'name' => $name, 'str' => $str, 'cat_type' => $cat_type, 'default' => $default);
	}

	/**
	 * Adds a calendar date selector
	 */
	function addDateInterval($namefrom, $nameto, $str)
	{
		$this->yui_dateinterval = true;
		$this->elems[] = array('type' => 'DATEINTERVAL', 'namefrom' => $namefrom, 'nameto' => $nameto, 'str' => $str);
	}

	function addCaptcha($objectinstance)
	{
		$this->elems[] = array('type' => 'CAPTCHA', 'obj' => $objectinstance);
	}

	/**
	 * Renders the form in XHTML
	 */
	function render()
	{
		global $h;

		if (!$this->objectinstance && !function_exists($this->handler))
			die('FATAL: xhtml_form() does not have a defined data handler');

		$res = '';
		if ($this->yui_dateinterval) {
			//use http://developer.yahoo.com/yui/articles/hosting/ to generate urls:
			$res .= '<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/combo?2.8.0r4/build/calendar/assets/skins/sam/calendar.css">';
			$res .= '<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js&2.8.0r4/build/calendar/calendar-min.js"></script>';
		}

		if ($this->yui_richedit) {
			$res .= '<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.0r4/build/assets/skins/sam/skin.css"> ';
			$res .= '<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js&2.8.0r4/build/container/container_core-min.js&2.8.0r4/build/menu/menu-min.js&2.8.0r4/build/element/element-min.js&2.8.0r4/build/button/button-min.js&2.8.0r4/build/editor/editor-min.js"></script> ';
		}

		$res .= xhtmlForm($this->name, '', 'post', $this->enctype);

		$res .= '<table cellpadding="10" cellspacing="0" border="1">';

		foreach ($this->elems as $e)
		{
			//fills in form with previous entered data
			if (!empty($e['name']) && isset($this->formData[$e['name']]))
				$e['default'] = $this->formData[$e['name']];

			$res .= '<tr>';
			switch ($e['type']) {
			case 'HIDDEN':
				$res .= xhtmlHidden($e['name'], $e['value']);
				break;

			case 'INPUT':
				if ($e['str']) {
					$res .= '<td>'.$e['str'].'</td>';
					$res .= '<td>'.xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
				} else {
					$res .= '<td colspan="2">'.xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
				}
				break;

			case 'CHECKBOX':
				$res .= '<td colspan="2">'.xhtmlCheckbox($e['name'], $e['str'], $e['default'], $e['checked']).'</td>';
				break;

			case 'TEXTAREA':
				$res .= '<td>'.$e['str'].'</td>';
				$res .= '<td>'.xhtmlTextarea($e['name'], $e['default'], $e['width'], $e['height']).'</td>';
				break;

			case 'TEXT':
				$res .= '<td colspan="2">'.$e['str'].'</td>';
				break;

			case 'DROPDOWN':
				$res .= '<td>'.$e['str'].'</td>';
				$res .= '<td>'.xhtmlSelectArray($e['name'], $e['arr'], $e['default']).'</td>';
				break;

			case 'RADIO':
				$res .= '<td>'.$e['str'].'</td>';
				$res .= '<td>'.xhtmlRadioArray($e['name'], $e['arr'], $e['default']).'</td>';
				break;

			case 'SUBMIT':
				$res .= '<td colspan="2">'.xhtmlSubmit($e['str']).'</td>';
				break;

			case 'CATEGORY':

				$cat = new CategoryList($e['cat_type']);
				$cat->setOwner($h->session->id);

				$res .= '<td>'.$e['str'].'</td>';
				$res .= '<td>';
				$res .= xhtmlSelectArray($e['name'], $cat->getKeyVals(), $e['default']).' ';
				//add new category widget
				$res .= '<a href="#" onClick="toggle_element(\'cd_new_'.$e['name'].'\');toggle_enabled_element(\'new_'.$e['name'].'\');">'.coreButton('Add').'</a>';
				$res .= '<span id="cd_new_'.$e['name'].'" style="display:none;">';
				$res .= xhtmlInput('new_'.$e['name'], 'new category', 15, 0, true);
				$res .= '</span>';
				$res .= '</td>';
				break;

			case 'DATEINTERVAL':
				$res .= '<td colspan="2">';

				$res .= '<div id="cal1Container"></div>';
				$res .= '<div style="clear:both"></div>';

				$res .= xhtmlInput($e['namefrom']).' - '.xhtmlInput($e['nameto']).'<br/>';

				//XXX ability att ange div id och input fält namn:
				$res .= file_get_contents('core_dev/js/yui_dateinterval.js');
				$res .= '</td>';
				break;

			case 'RICHEDIT':
				//http://developer.yahoo.com/yui/editor/
				$res .= '<td>'.$e['str'].'</td>';
				$res .= '<td>'.xhtmlTextarea($e['name'], $e['default'], $e['width'], $e['height']).'</td>';

				//XXX: how to disable "insert image" button?
				//XXX: how to change title from "Text Editing Tools" ?
				$res .=
				'<script type="text/javascript">'.
				//'var myEditor = new YAHOO.widget.Editor("'.$e['name'].'", {'.
				'var myEditor = new YAHOO.widget.SimpleEditor("'.$e['name'].'", {'.
				'width: "'.$e['width'].'px",'.
				'height: "'.$e['height'].'px",'.
				'dompath: true,'. //Turns on the bar at the bottom
				'animate: true,'. //Animates the opening, closing and moving of Editor windows
				'handleSubmit: true,'. //editor will attach itself to the textareas parent form's submit handler
				'});'.
				'myEditor.render();'.
				'</script>';
				break;

			case 'CAPTCHA':
				$res .= '<td colspan="2">';
				$res .= $e['obj']->render();
				$res .= '</td>';
				break;

			default:
				$res .= '<h1>'.$e['type'].' not implemented</h1>';
				break;
			}
			$res .= '</tr>';
		}

		$res .= '</table>';

		$res .= xhtmlFormClose();
		return $res;
	}
}

?>
