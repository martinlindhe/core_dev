<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip


//XXX TODO require that all objects added with add() method implements/extends some "IXhtmlComponent" interface or similar  (see/rewrite IXmlComponent)
//TODO soon: rewrite internal form field representation to use objects passed to add() method


//TODO: if not all form fields is set in a post handling, then dont read any, so callbacks can assume all indexes are set
//FIXME: dateinterval selection is not auto-filled on next request, see handle() ???

require_once('ErrorHandler.php');
require_once('CaptchaRecaptcha.php');
require_once('output_xhtml.php');

require_once('XhtmlComponentInput.php');
require_once('XhtmlComponentSubmit.php');

require_once('YuiAutocomplete.php');
require_once('YuiDate.php');
require_once('YuiDatePopup.php');
require_once('YuiDateInterval.php');
require_once('YuiRichedit.php');

class XhtmlForm
{
    protected $file_upload      = false;
    protected $handled          = false;   ///< is set to true when form data has been processed by callback function
    protected $name;
    protected $post_handler;               ///< function to call as POST callback
    protected $objectinstance   = false;
    protected $formData         = array();
    protected $elems            = array();
    protected $url_handler;                ///< sends form to a different url
    protected $auto_code        = true;    ///< automatically encode/decode form data using urlencode
    protected $using_captcha    = false;
    protected $focus_element;

    function __construct($name = '', $url_handler = '')
    {
        $this->name        = $name;
        $this->url_handler = $url_handler;
    }

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
     * Activates javascript that auto-focuses on specified input field
     */
    function setFocus($s)
    {
        foreach ($this->elems as $e) {
            if (isset($e['obj']) && is_object($e['obj'])) {
                if ($e['obj']->name = $s) {
                    $this->focus_element = $s;
                    return true;
                }
            } else if ($e['name'] == $s) {
                $this->focus_element = $s;
                return true;
            }
        }

        throw new Exception ('element '.$s.' not defined');
    }

    /**
     * Processes the form
     */
    function handle()
    {
        if ($this->using_captcha) {
            $captcha = CaptchaRecaptcha::getInstance();
            $captcha->verify();
        }

        $p = array();
        if (!empty($_POST))
            foreach ($_POST as $key => $val)
                foreach ($this->elems as $e) {
                    if (isset($e['obj']) && is_object($e['obj'])) {

                        if (!isset($e['obj']->name))
                            continue;

                        if ($e['obj']->name == $key) {
                            if (is_array($val)) {
                                foreach ($val as $idx => $v)
                                    $val[ $idx ] = $this->auto_code ? urldecode($v) : $v;
                                $p[ $key ] = $val;
                            }
                            else
                                $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                        }

                        // handle input arrays
                        if ($e['obj']->name == $key.'[]') {
                            if (is_array($val)) {
                                foreach ($val as $idx => $v)
                                    $val[ $idx ] = $this->auto_code ? urldecode($v) : $v;
                                $p[ $key ] = $val;
                            }
                            else
                                $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                        }
                        continue;
                    }

                    switch ($e['type']) {
                    case 'DATEINTERVAL':
                        if (!empty($e['namefrom']) && $e['namefrom'] == $key ||
                            !empty($e['nameto'])   && $e['nameto']   == $key)
                            $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                        break;

                    default:
                        if (empty($e['name']))
                            break;

                        if ($e['name'] == $key) {
                            if (is_array($val)) {
                                foreach ($val as $idx => $v)
                                    $val[ $idx ] = $this->auto_code ? urldecode($v) : $v;
                                $p[ $key ] = $val;
                            }
                            else
                                $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                        }

                        // handle input arrays
                        if ($e['name'] == $key.'[]') {
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
                foreach ($this->elems as $e) {
                    d($e);
                    if (isset($e['obj']) && is_object($e['obj']) && $e['obj']->name == $key) {

                    }
                    if (!empty($e['name']) && !isset($_POST[$e['name']]) && $e['name'] == $key)
                        $p[ $key ] = $this->auto_code ? urldecode($val) : $val;
                }

        $page = XmlDocumentHandler::getInstance();

        // include FILES uploads
        if ($this->file_upload) {
            foreach ($this->elems as $e) {
                if ($e['type'] == 'FILE' && !empty($_FILES[ $e['name'] ]) ) {

                    $key = $_FILES[ $e['name'] ];

                    // ignore empty file uploads
                    if (!$key['name'])
                        continue;

                    if (!is_uploaded_file($key['tmp_name'])) {
                        $error->add('Upload failed for file '.$key['name'] );
                        continue;
                    }

                    $dst_file = $page->getUploadRoot().$key['name'];

                    if (move_uploaded_file($key['tmp_name'], $dst_file))
                        chmod($dst_file, 0777);
                    else
                        throw new Exception ('Failed to move file from '.$key['tmp_name'].' to '.$dst_file);

                    $key['name'] = $dst_file;

                    $p[ $e['name'] ] = $key;

                    unset($_FILES[ $e['name'] ]);    //to avoid further processing of this file upload elsewhere
                }
            }
        }

        if (!$p) return false;

        $this->formData = $p;

        if ($this->objectinstance)
            $call = array($this->objectinstance, $this->post_handler);
        else
            $call = $this->post_handler;

        $error = ErrorHandler::getInstance();

        if (!$error->getErrorCount())
            if (call_user_func($call, $this->formData, $this))
                $this->handled = true;

        if ($error->getErrorCount()) {
            echo $error->render(true).'<br/>';
            return false;
        }

        if ($this->handled)
            return true;

        return false;
    }

    /**
     * Adds a object to the form
     */
    function add($o, $str = '')
    {
        if (!is_object($o))
            throw new Exception ('not an object');

        $this->elems[] = array('obj' => $o, 'str' => $str);
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
        $o = new XhtmlComponentInput();
        $o->name  = $name;
        $o->value = $val;
        $o->size  = $size;

        $this->elems[] = array('obj' => $o, 'str' => $str);
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
    function addSubmit($title)
    {
        $o = new XhtmlComponentSubmit();
        $o->title = $title;

        $this->elems[] = array('obj' => $o, 'str' => '');
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
     * Adds a date selector popup
     */
    function addDatePopup($name, $str = '', $init = '')
    {
        $this->elems[] = array('type' => 'DATEPOPUP', 'name' => $name, 'str' => $str, 'init' => $init);
    }

    /**
     * Adds a date interval selector
     */
    function addDateInterval($namefrom, $nameto, $str = '', $init_from = '', $init_to = '')
    {
        $this->elems[] = array('type' => 'DATEINTERVAL', 'namefrom' => $namefrom, 'nameto' => $nameto, 'str' => $str, 'init_from' => $init_from, 'init_to' => $init_to);
    }

    /**
     * Adds a captcha
     */
    function addCaptcha()
    {
        $this->using_captcha = true;
        $this->elems[] = array('type' => 'CAPTCHA');
    }

    /**
     * Adds a file uploader
     */
    function addFile($name, $str = '')
    {
         $this->file_upload = true;
        $this->elems[] = array('type' => 'FILE', 'name' => $name, 'str' => $str);
    }

    /**
     * Renders the form in XHTML
     */
    function render()
    {
        if (!$this->url_handler && !$this->objectinstance && !function_exists($this->post_handler))
            throw new Exception ('FATAL: XhtmlForm does not have a defined data handler');

        $enctype = $this->file_upload ? 'multipart/form-data' : '';

        if (!$this->name)
            $this->name = 'frm'.mt_rand(1,999999);

        $header = XhtmlHeader::getInstance();

        if ($this->focus_element)
            $header->embedJsOnload('document.'.$this->name.'.'.$this->focus_element.'.focus();');

        $res = xhtmlForm($this->name, $this->url_handler, 'post', $enctype);

        $res .= '<table cellpadding="10" cellspacing="0" border="1">';

        foreach ($this->elems as $e)
        {
            $res .= '<tr>';

            if (isset($e['obj']) && is_object($e['obj'])) {
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= $e['obj']->render().'</td>';
                $res .= '</tr>';
                continue;
            }

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

            switch ($e['type']) {
            case 'HIDDEN':
                $res .= xhtmlHidden($e['name'], $e['value']);
                break;

            case 'CHECKBOX':
                $res .= '<td colspan="2">'.xhtmlCheckbox($e['name'], $e['str'], $e['default'], $e['checked']).'</td>';
                break;

            case 'TEXTAREA':
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= xhtmlTextarea($e['name'], $e['default'], $e['width'], $e['height']).'</td>';
                break;

            case 'RICHEDIT':
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= xhtmlTextarea($e['name'], $e['default'], 1, 1).'</td>';

                $richedit = new YuiRichedit();
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
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= xhtmlSelectArray($e['name'], $e['arr'], $e['default']).'</td>';
                break;

            case 'RADIO':
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= xhtmlRadioArray($e['name'], $e['arr'], $e['default']).'</td>';
                break;

            case 'LISTBOX':
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= xhtmlSelectMultiple($e['name'], $e['arr'], $e['default']).'</td>';
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

                $dateselect = new YuiDate();
                $dateselect->setDivName('cal1Container');
                $dateselect->setName($e['name']);

                $e['name_val'] = !empty($this->formData[$e['name']]) ? $this->formData[$e['name']] : $e['init'];

                $dateselect->setSelection($e['name_val']);
                $res .= $dateselect->render();

                $res .= '</td>';
                break;

            case 'DATEPOPUP':
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';

                $res .= xhtmlInput($e['name'], $e['init'], 8).' ';

                $dateselect = new YuiDatePopup();
                $dateselect->setName($e['name']);

                $dateselect->setSelection($e['init']);
                $res .= $dateselect->render();

                $res .= '</td>';
                break;

            case 'DATEINTERVAL':
                $res .= '<td colspan="2">';
                if ($e['str']) $res .= $e['str'].'<br/><br/>';

                $dateselect = new YuiDateInterval();
                $dateselect->setNameFrom($e['namefrom']);
                $dateselect->setNameTo($e['nameto']);

                $e['namefrom_val'] = !empty($this->formData[$e['namefrom']]) ? $this->formData[$e['namefrom']] : $e['init_from'];
                $e['nameto_val']   = !empty($this->formData[$e['nameto']])   ? $this->formData[$e['nameto']]   : $e['init_to'];

                $dateselect->setSelection($e['namefrom_val'], $e['nameto_val']);
                $res .= $dateselect->render().'<br/>';

                $res .= xhtmlInput($e['namefrom']).' - '.xhtmlInput($e['nameto']);

                $res .= '</td>';
                break;

            case 'CAPTCHA':
                $captcha = CaptchaRecaptcha::getInstance();

                $res .= '<td colspan="2">';
                $res .= $captcha->render();
                $res .= '</td>';
                break;

            case 'FILE':
                $res .= $e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">';
                $res .= xhtmlFile($e['name']);
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
