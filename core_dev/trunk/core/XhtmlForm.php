<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('ErrorHandler.php');
require_once('output_xhtml.php');

require_once('constants.php');
require_once('FileHelper.php');
require_once('XhtmlComponent.php');

class XhtmlForm
{
    protected $file_upload      = false;
    protected $handled          = false;   ///< is set to true when form data has been processed by callback function
    protected $name;
    protected $post_handler;               ///< function to call as POST callback
    protected $objectinstance   = false;
    protected $form_data        = array();
    protected $elems            = array();
    protected $url_handler;                ///< sends form to a different url
    protected $using_captcha    = false;
    protected $focus_element;

    protected $css_table        = 'border:1px solid;';

    protected $js_onsubmit;                ///< js to execute on form submit

    function __construct($name = '', $url_handler = '')
    {
        if ($name)
            $this->name = $name;
        else
            $this->name = 'frm'.mt_rand();

        $this->url_handler = $url_handler;
    }

    function getName() { return $this->name; }

    function onSubmit($s) { $this->js_onsubmit = $s; }

    function cssTable($s) { $this->css_table = $s; }

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
    }

    /** Activates javascript that auto-focuses on specified input field */
    function setFocus($s)
    {
        foreach ($this->elems as $e)
        {
            if (isset($e['obj']) && is_object($e['obj'])) {
                if ($e['obj']->name == $s) {
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
     * Processes the form submit. Is called automatically from render() if not called before
     * @return true if handled
     */
    public function handle()
    {
        $p = array();

        // fetch GET parameters before processing POST
        if (!empty($_GET))
            foreach ($_GET as $key => $val)
                foreach ($this->elems as $e)
                {
                    if (isset($e['obj']) && is_object($e['obj']) && $e['obj']->name == $key)
                        $p[ $key ] = htmlspecialchars_decode($val);
                }

        if (!empty($_POST))
            foreach ($_POST as $key => $val)
                foreach ($this->elems as $e)
                {
                    if (isset($e['obj']) && is_object($e['obj']))
                    {
                        if (!isset($e['obj']->name))
                            continue;

                        if ($e['obj']->name == $key)
                        {
                            if (is_array($val))
                            {
                                foreach ($val as $idx => $v)
                                    $val[ $idx ] = htmlspecialchars_decode($v);
                                $p[ $key ] = $val;
                            }
                            else
                            {
                                $p[ $key ] = htmlspecialchars_decode($val);
                            }
                        }
                        else if ($e['obj'] instanceof YuiDateInterval)
                        {
                            if ($e['obj']->name.'_from' == $key)
                            {
                                $e['obj']->selectFrom($val);
                                $p[ $key ] = htmlspecialchars_decode($val);
                            }

                            if ($e['obj']->name.'_to' == $key)
                            {
                                $e['obj']->selectTo($val);
                                $p[ $key ] = htmlspecialchars_decode($val);
                            }

                        } else if ($e['obj']->name == $key.'[]')
                        {
                            // handle input arrays
                            if (is_array($val)) {
                                foreach ($val as $idx => $v)
                                    $val[ $idx ] = htmlspecialchars_decode($v);
                                $p[ $key ] = $val;
                            } else
                                $p[ $key ] = htmlspecialchars_decode($val);
                        }
                    }
                }

        // include FILES uploads
        foreach ($this->elems as $e)
        {
            if (isset($e['obj']) && is_object($e['obj']) && $e['obj'] instanceof XhtmlComponentFile && !empty($_FILES[ $e['obj']->name ]))
            {
                $key = $_FILES[ $e['obj']->name ];

                $p[ $e['obj']->name ] = $key;

                // to avoid further processing of this file upload elsewhere
                unset($_FILES[ $e['obj']->name ]);
            }
        }

        if ($this->using_captcha && !empty($_POST)) {
            $captcha = new Recaptcha();
            if (!$captcha->verify())
                return false;
        }

        if (!$p) return false;

        $this->form_data = $p;

        if ($this->objectinstance)
            $call = array($this->objectinstance, $this->post_handler);
        else
            $call = $this->post_handler;

        $error = ErrorHandler::getInstance();

        if (!$error->getErrorCount())
            if (call_user_func($call, $this->form_data, $this))
                $this->handled = true;

        if ($error->getErrorCount())
            return false;

        if ($this->handled)
            return true;

        return false;
    }

    /** Adds a object to the form */
    function add($o, $text = '')
    {
        if (!is_object($o))
            throw new Exception ('not an object');

        if (!$o instanceof XhtmlComponent)
            throw new exception ('obj must extend from XhtmlComponent');

        if ($o instanceof XhtmlComponentFile)
            $this->file_upload = true;

        $this->elems[] = array('obj' => $o, 'str' => $text);
    }

    /** Adds a hidden input field to the form */
    function addHidden($name, $val)
    {
        $o = new XhtmlComponentHidden();
        $o->name  = $name;
        $o->value = $val;
        $this->add($o);
    }

    /** Adds a input field to the form */
    function addInput($name, $text, $val = '', $size = 0, $maxlen = 0)
    {
        $o = new XhtmlComponentInput();
        $o->name   = $name;
        $o->value  = $val;
        $o->size   = $size;
        $o->maxlen = $maxlen;
        $this->add($o, $text);
   }

    /** Adds a password field to the form */
    function addPassword($name, $text, $size = 0, $maxlen = 0)
    {
        $o = new XhtmlComponentPassword();
        $o->name   = $name;
        $o->size   = $size;
        $o->maxlen = $maxlen;
        $this->add($o, $text);
   }

    /** Adds a checkbox field to the form */
    function addCheckbox($name, $text, $checked = false, $val = '1')
    {
        $o = new XhtmlComponentCheckbox();
        $o->name  = $name;
        $o->title = $text;
        $o->value = $val;
        $o->checked = $checked;
        $this->add($o);
    }

    /** Adds a textarea to the form */
    function addTextarea($name, $text, $val = '', $width = 300, $height = 70)
    {
        $o = new XhtmlComponentTextarea();
        $o->name   = $name;
        $o->value  = $val;

        $o->style =
            'font:14px arial;'.
            ($width  ? 'width:'.$width.'px;' : '').
            ($height ? 'height:'.$height.'px;' : '');

        $this->add($o, $text);
    }

    /** Adds a richedit textarea to the form */
    function addRichedit($name, $text, $val = '', $width = 0, $height = 0)
    {
        if (!$width)
            $width = 440;

        if (!$height)
            $height = 200;

        $o = new YuiRichedit();
        $o->name   = $name;
        $o->value  = $val;
        $o->width  = $width;
        $o->height = $height;
        $this->add($o, $text);
    }

    /** Adds a text string to the form */
    function addText($text, $text2 = '')
    {
        $o = new XhtmlComponentText();
        $o->value = $text;
        $this->add($o, $text2);
    }

    /** Adds a submit button to the form */
    function addSubmit($title, $css = '')
    {
        $o = new XhtmlComponentSubmit();
        $o->title = $title;
        $o->style = $css ? $css : '';
        $this->add($o);
    }

    /**
     * Adds a select dropdown list to the form
     * @param $arr array with id=>name pairs
     */
    function addDropdown($name, $text, $arr, $selected = '')
    {
        $o = new XhtmlComponentDropdown();
        $o->name    = $name;
        $o->value   = $selected;
        $o->options = $arr;
        $this->add($o, $text);
    }

    function addRadio($name, $text, $arr, $default = '')
    {
        $o = new XhtmlComponentRadio();
        $o->name    = $name;
        $o->value   = $default;
        $o->options = $arr;
        $this->add($o, $text);
    }

    /** Adds a multi-select listbox */
    function addListbox($name, $text, $arr, $default = '')
    {
        $o = new XhtmlComponentListbox();
        $o->name    = $name;
        $o->value   = $default;
        $o->options = $arr;
        $this->add($o, $text);
    }

    /** Adds a file uploader */
    function addFile($name, $text = '', $type = USER)
    {
        $o = new XhtmlComponentFile();
        $o->name = $name;
        $o->type = $type;
        $this->add($o, $text);
    }

    function addAutocomplete($name, $text, $url, $result_fields)
    {
        $o = new YuiAutocomplete();
        $o->setName($name);
        $o->setXhrUrl($url);
        $o->setResultFields( $result_fields );
        $this->add($o, $text);
    }

    /** Adds a date selector */
    function addDate($name, $text = '', $select = '')
    {
        $o = new YuiDate();
        $o->setName($name);
        $o->setSelection($select);
        $this->add($o, $text);
    }

    /** Adds a date selector popup */
    function addDatePopup($name, $text = '', $select = '')
    {
        $o = new YuiDatePopup();
        $o->setName($name);
        $o->setSelection($select);
        $this->add($o, $text);
    }

    /** Adds a date interval selector */
    function addDateInterval($name, $text = '', $select_from = '', $select_to = '')
    {
        $o = new YuiDateInterval();
        $o->setName($name);
        $o->setSelection($select_from, $select_to);
        $this->add($o, $text);
    }

    /** Adds a captcha */
    function addCaptcha()
    {
        $this->using_captcha = true;

        $o = new Recaptcha();
        $this->add($o);
    }

    /** Renders the form in XHTML */
    function render()
    {
        if (!$this->url_handler && !$this->objectinstance && !function_exists($this->post_handler))
        {
            if (!function_exists($this->post_handler))
                throw new Exception ('FATAL: XhtmlForm post handler: function "'.$this->post_handler.'" is not declared!');

            throw new Exception ('FATAL: XhtmlForm does not have a defined data handler');
        }

        if (!$this->name)
            throw new Exception ('We need a form name!');

        if (!$this->handled)
            $this->handle();

        $res = '';

        $error = ErrorHandler::getInstance();

        if ($error->getErrorCount())
            $res .= $error->render(true);

        $header = XhtmlHeader::getInstance();

        if ($this->focus_element)
            $header->embedJsOnload('document.'.$this->name.'.'.$this->focus_element.'.focus();');

        $res .=
        '<form'.
        ' action="'.$this->url_handler.'"'.
        ' method="post"'.
        ' name="'.$this->name.'"'.
        ($this->file_upload ? '" enctype="multipart/form-data"'     : '').
        ($this->js_onsubmit ? '" onsubmit="'.$this->js_onsubmit.'"' : '').
        '>';

        $res .=
        '<table'.
        ' cellpadding="10"'.
        ' cellspacing="0"'.
        ($this->css_table ? ' style="'.$this->css_table.'"' : '').
        '>';

        // fills in form with previous entered data        XXXXX move into handle() ?
        foreach ($this->elems as $e)
        {
            if (!isset($e['obj']))
                throw new Exception ('ehjohohohoh: '.$e['obj']);

            if (!is_object($e['obj']))
                throw new Exeption ('dont do that');

            if (isset($e['obj']->value))
                $e['obj']->value = htmlspecialchars($e['obj']->value);

            if ($e['obj'] instanceof XhtmlComponentHidden) {
                $res .= $e['obj']->render();
                continue;
            }

            if ($e['obj'] instanceof XhtmlComponentCheckbox)
                if (isset($this->form_data[ $e['obj']->name ]))
                    $e['obj']->checked = $this->form_data[ $e['obj']->name ];
            else
                if (!empty($this->form_data[ $e['obj']->name ]) && property_exists($e['obj'], 'value') )
                    $e['obj']->value = $this->form_data[ $e['obj']->name ];

            $res .=
            '<tr>'.
            ($e['str'] ? '<td>'.$e['str'].'</td><td>' : '<td colspan="2">').
            $e['obj']->render().'</td>'.
            '</tr>';
        }

        $res .= '</table>';

        $res .= '</form>';
        return $res;
    }

}

?>
