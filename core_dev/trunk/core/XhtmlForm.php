<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: if not all form fields is set in a post handling, then dont read any, so callbacks can assume all indexes are set
//FIXME: dateinterval selection is not auto-filled on next request, see handle()

require_once('client_captcha.php');
require_once('output_xhtml.php');

require_once('yui_date.php');
require_once('yui_dateinterval.php');
require_once('yui_richedit.php');

class XhtmlForm
{
    private $enctype          = '';     ///< TODO: set multipart type if form contains file upload parts
    private $handled          = false;  ///< is set to true when form data has been processed by callback function
    private $name;
    private $post_handler;    ///< function to call as POST callback
    private $objectinstance   = false;
    private $formData         = array();
    private $elems            = array();
    private $success          = '';
    private $error            = 'Submitted form was rejected!';
    private $url_handler;     ///< sends form to a different url
    private $form_method;     ///< get/post
    private $auto_code        = true; ///< automatically encode/decode form data using urlencode

    function __construct($name = '', $url_handler = '', $form_method = 'post')
    {
        $this->name        = $name;
        $this->url_handler = $url_handler;
        $this->form_method = $form_method;
    }

    function setError($s) { $this->error = $s; }

    function setSuccess($s) { $this->success = $s; }

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
        if ($this->url_handler)
            throw new Exception ('Cant use setHandler together with a separate url_handler');

        $this->post_handler = $f;

        if (is_object($objectinstance))
            $this->objectinstance = $objectinstance;

        $this->handle();
    }

    /**
     * Processes the form
     */
    function handle()
    {
        $p = array();
        if (!empty($_POST))
            foreach ($_POST as $key => $val)
                foreach ($this->elems as $row) {
                    switch ($row['type']) {
                    case 'DATEINTERVAL':
                        if (!empty($row['namefrom']) && $row['namefrom'] == $key ||
                            !empty($row['nameto'])   && $row['nameto']   == $key)
                            $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                        break;

                    default:
                        if (!empty($row['name']) && $row['name'] == $key) {
                            if (is_array($val)) {
                                foreach ($val as $idx => $v)
                                    $val[ $idx ] = $this->auto_code ? urldecode($v) : $v;
                                $p[ $key ] = $val;
                            }
                            else
                                $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                        }
                        break;
                    }
                }

        //catch named GET parameters if no POST parameters are found

        if (!empty($_GET))
            foreach ($_GET as $key => $val)
                foreach ($this->elems as $row)
                    if (!empty($row['name']) && !isset($_POST[$row['name']]) && $row['name'] == $key)
                        $p[ $key ] = $this->auto_code ? urldecode($val) : $val;

        if (!$p) return false;
/*
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
*/
        $this->formData = $p;

        if ($this->objectinstance)
            $call = array($this->objectinstance, $this->post_handler);
        else
            $call = $this->post_handler;

        if (call_user_func($call, $this->formData, $this))
            $this->handled = true;

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
        $this->elems[] = array('type' => 'RICHEDIT', 'name' => $name, 'str' => $str, 'default' => $val, 'width' => $width, 'height' => $height);
    }

    /**
     * Adds a text string to the form
     */
    function addText($str, $str2 = '')
    {
        $this->elems[] = array('type' => 'TEXT', 'str' => $str, 'str2' => $str2);
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
    function addDropdown($name, $str, $arr, $default = '')
    {
        $this->elems[] = array('type' => 'DROPDOWN', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
    }

    function addRadio($name, $str, $arr, $default = '')
    {
        $this->elems[] = array('type' => 'RADIO', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
    }

    /**
     * Adds a multi-select listbox
     */
    function addListbox($name, $str, $arr, $default = '')
    {
        //FIXME: $default param is ignored by xhtmlSelectMultiple()
        $this->elems[] = array('type' => 'LISTBOX', 'name' => $name, 'str' => $str, 'arr' => $arr, 'default' => $default);
    }

    /**
     * Adds a category to the form
     * @param $cat_type category type
     */
    function addCategory($name, $str, $cat_type, $default = '')
    {
        $this->elems[] = array('type' => 'CATEGORY', 'name' => $name, 'str' => $str, 'cat_type' => $cat_type, 'default' => $default);
    }

    /**
     * Adds a date selector
     */
    function addDate($name, $str = '', $init = '')
    {
        $this->elems[] = array('type' => 'DATE', 'name' => $name, 'str' => $str, 'init' => $init);
    }

    /**
     * Adds a date interval selector
     */
    function addDateInterval($namefrom, $nameto, $str = '', $init_from = '', $init_to = '')
    {
        $this->elems[] = array('type' => 'DATEINTERVAL', 'namefrom' => $namefrom, 'nameto' => $nameto, 'str' => $str, 'init_from' => $init_from, 'init_to' => $init_to);
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
        if (!$this->url_handler && !$this->objectinstance && !function_exists($this->post_handler))
            throw new Exception ('FATAL: XhtmlForm does not have a defined data handler');

        $res = xhtmlForm($this->name, $this->url_handler, $this->form_method, $this->enctype);

        $res .= '<table cellpadding="10" cellspacing="0" border="1">';

        foreach ($this->elems as $e)
        {
            //fills in form with previous entered data
            switch ($e['type']) {
            case 'CHECKBOX':
                //dont set a unset checkbox to value 0, it breaks the form
                if (!empty($e['name']) && !empty($this->formData[$e['name']]))
                    $e['checked'] = true;
                break;

            default:
                if (!empty($e['name']) && isset($this->formData[$e['name']]))
                    $e['default'] = $this->formData[$e['name']];
            }

            if ($this->auto_code && isset($e['value']))
                $e['value'] = urlencode($e['value']);

            $res .= '<tr>';
            switch ($e['type']) {
            case 'HIDDEN':
                $res .= xhtmlHidden($e['name'], $e['value']);
                break;

            case 'CHECKBOX':
                $res .= '<td colspan="2">'.xhtmlCheckbox($e['name'], $e['str'], $e['default'], $e['checked']).'</td>';
                break;

            case 'INPUT':
                if ($e['str']) {
                    $res .= '<td>'.$e['str'].'</td><td>';
                } else {
                    $res .= '<td colspan="2">';
                }
                $res .= xhtmlInput($e['name'], $e['default'], $e['size']).'</td>';
                break;

            case 'TEXTAREA':
                if ($e['str']) {
                    $res .= '<td>'.$e['str'].'</td><td>';
                } else {
                    $res .= '<td colspan="2">';
                }
                $res .= xhtmlTextarea($e['name'], $e['default'], $e['width'], $e['height']).'</td>';
                break;

            case 'RICHEDIT':
                if ($e['str']) {
                    $res .= '<td>'.$e['str'].'</td><td>';
                } else {
                    $res .= '<td colspan="2">';
                }
                $res .= xhtmlTextarea($e['name'], $e['default'], 1, 1).'</td>';

                $richedit = new yui_richedit();
                $richedit->setInputName($e['name']);
                $richedit->setWidth($e['width']);
                $richedit->setHeight($e['height']);
                $res .= $richedit->render();
                break;

            case 'TEXT':
                if ($e['str2'])
                    $res .= '<td>'.$e['str'].'</td><td>'.$e['str2'].'</td>';
                else
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

            case 'LISTBOX':
                $res .= '<td>'.$e['str'].'</td>';
                $res .= '<td>'.xhtmlSelectMultiple($e['name'], $e['arr'], $e['default']).'</td>';
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

            case 'DATE':
                $res .= '<td colspan="2">';
                if ($e['str']) $res .= $e['str'].'<br/><br/>';
                $res .= '<div id="cal1Container"></div>';
                $res .= '<div style="clear:both"></div>';

                $res .= xhtmlInput($e['name']).'<br/>';

                $dateselect = new yui_date();
                $dateselect->setDivName('cal1Container');
                $dateselect->setName($e['name']);

                $e['name_val'] = !empty($this->formData[$e['name']]) ? $this->formData[$e['name']] : $e['init'];

                $dateselect->setSelection($e['name_val']);
                $res .= $dateselect->render();

                $res .= '</td>';
                break;

            case 'DATEINTERVAL':
                $res .= '<td colspan="2">';
                if ($e['str']) $res .= $e['str'].'<br/><br/>';
                $res .= '<div id="cal2Container"></div>';
                $res .= '<div style="clear:both"></div>';

                $res .= xhtmlInput($e['namefrom']).' - '.xhtmlInput($e['nameto']).'<br/>';

                $dateselect = new yui_dateinterval();
                $dateselect->setDivName('cal2Container');
                $dateselect->setNameFrom($e['namefrom']);
                $dateselect->setNameTo($e['nameto']);

                $e['namefrom_val'] = !empty($this->formData[$e['namefrom']]) ? $this->formData[$e['namefrom']] : $e['init_from'];
                $e['nameto_val']   = !empty($this->formData[$e['nameto']])   ? $this->formData[$e['nameto']]   : $e['init_to'];

                $dateselect->setSelection($e['namefrom_val'], $e['nameto_val']);
                $res .= $dateselect->render();

                $res .= '</td>';
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
