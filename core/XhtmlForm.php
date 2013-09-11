<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant forms
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

//STATUS: wip

//FIXME: "fill form from previous submit" code is broken!
//XXX: can the "fill form with previous entered data code from render() method move to handle() ????

//TODO low prio: remove need of "using_captcha" variable, it can be figured out by checking object types

namespace cd;

require_once('ErrorHandler.php');
require_once('output_xhtml.php');
require_once('constants.php');
require_once('File.php');
require_once('XhtmlComponent.php');

class XhtmlForm
{
    protected $name;                     ///< form name
    protected $id;                       ///< form id
    protected $post_handler;             ///< function to call as POST callback
    protected $form_data      = array();
    protected $elems          = array();
    protected $url_handler;              ///< sends form to a different url
    protected $autocomplete   = true;    ///< tell browser to suggest autocomplete data of this form?
    protected $title;                    ///< optional form title

    protected $file_upload    = false;
    protected $handled        = false;   ///< true when form data has been processed by callback function
    protected $using_captcha  = false;
    protected $focus_element;

    protected $css_table      = 'border:1px solid;';
    protected $js_onsubmit;              ///< js to execute on form submit

    function __construct($name = '', $id = '', $url_handler = '')
    {
        if ($name)
            $this->name = $name;
        else
            $this->name = 'frm'.mt_rand();

        $this->id = $id;
        $this->url_handler = $url_handler;
    }

    function getName() { return $this->name; }

    function setId($s) { $this->id = $s; }
    function onSubmit($s) { $this->js_onsubmit = $s; }
    function cssTable($s) { $this->css_table = $s; }

    function disableAutocomplete() { $this->autocomplete = false; }

    /**
     * Defines the function/object->method that will handle form submit processing
     * Call this function when all form elements have been added in order
     * to fetch GET/POST parameters from previous page view
     *
     * @param $f function/method name to process form data
     * @param $o object
     */
    function setHandler($f, $o = false)
    {
        if ($this->url_handler)
            throw new \Exception ('Cant use setHandler together with a separate url_handler');

        if (is_object($o))
            $this->post_handler = array($o, $f);
        else {
            if ($f && function_exists($f))
                $this->post_handler = $f;
            else if ($f && function_exists(__NAMESPACE__.'\\'.$f))
                $this->post_handler = __NAMESPACE__.'\\'.$f;
            else
                throw new \Exception ('function '.$f.' is not defined');
        }
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

        throw new \Exception ('element "'.$s.'" not found');
    }

    function setTitle($s) { $this->title = $s; }

    /**
     * Processes the form submit. Is called automatically from render() if not called before
     * @return true if handled
     */
    public function handle()
    {
        $p = array();

        // fetch GET parameters before processing POST
        foreach ($_GET as $key => $val)
            foreach ($this->elems as $e)
            {
                if (!is_object($e['obj']))
                    throw new \Exception ('XXX not an obj!');

                if (!isset($e['obj']->name))
                    continue;

                if ($e['obj']->name == $key)
                    $p[ $key ] = htmlspecialchars_decode($val);
            }

        foreach ($_POST as $key => $val)
            foreach ($this->elems as $e)
            {
                if (!is_object($e['obj']))
                    throw new \Exception ('XXX not an obj!');

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
                    if (is_array($val))
                    {
                        foreach ($val as $idx => $v)
                            $val[ $idx ] = htmlspecialchars_decode($v);
                        $p[ $key ] = $val;
                    } else
                        $p[ $key ] = htmlspecialchars_decode($val);
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

        if ($this->using_captcha && !empty($_POST))
        {
            $captcha = new Recaptcha();
            if (!$captcha->verify())
                return false;
        }

        if (!$p)
            return false;

        $this->form_data = $p;

        $error = ErrorHandler::getInstance();

        if (!$error->getErrorCount() && $this->post_handler)
            if (call_user_func($this->post_handler, $this->form_data, $this))
                $this->handled = true;

        if ($error->getErrorCount())
            return false;

        return $this->handled;
    }

    /** Adds a object to the form */
    function add($o, $text = '', $second_obj = '')
    {
        if (!is_object($o))
            throw new \Exception ('not an object');

        if (!$o instanceof XhtmlComponent)
            throw new \Exception ('obj must extend from XhtmlComponent');

        if ($second_obj && !($second_obj instanceof XhtmlComponent))
            throw new \Exception ('second_obj must extend from XhtmlComponent');

        if ($o instanceof XhtmlComponentFile)
            $this->file_upload = true;

        $this->elems[] = array('obj' => $o, 'str' => $text, 'obj2' => $second_obj);
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
    function addInput($name, $text, $val = '', $width = 0, $maxlen = 0)
    {
        if (!is_numeric($width) || !is_numeric($maxlen))
            throw new \Exception ('bad input');

        $o = new XhtmlComponentInput();
        $o->name   = $name;
        $o->value  = $val;
        $o->width  = $width;
        $o->maxlen = $maxlen;
        $o->autocomplete = $this->autocomplete;
        $this->add($o, $text);
   }

    /** Adds a password field to the form */
    function addPassword($name, $text, $width = 0, $maxlen = 0)
    {
        if (!is_numeric($width) || !is_numeric($maxlen))
            throw new \Exception ('bad input');

        $o = new XhtmlComponentPassword();
        $o->name   = $name;
        $o->width  = $width;
        $o->maxlen = $maxlen;
        $this->add($o, $text);
   }

    /** Adds a textarea to the form */
    function addTextarea($name, $text = '', $val = '', $width = 300, $height = 70)
    {
        if (!is_numeric($width) || !is_numeric($height))
            throw new \Exception ('bad input');

        $o = new XhtmlComponentTextarea();
        $o->name   = $name;
        $o->value  = $val;
        $o->width  = $width;
        $o->height = $height;
        $o->style  = 'font:14px arial;';

        $this->add($o, $text);
    }

    /** Adds a checkbox field to the form */
    function addCheckbox($name, $text, $checked = false, $val = '1')
    {
        $o = new XhtmlComponentCheckbox();
        $o->name    = $name;
        $o->title   = $text;
        $o->value   = $val;
        $o->checked = $checked;
        $this->add($o);
    }

    /** Adds a text string to the form */
    function addText($text, $text2 = '')
    {
        $o = new XhtmlComponentText();
        $o->value = $text;
        $this->add($o, $text2);
    }

    /** Adds a submit button to the form */
    function addSubmit($title = 'Submit', $css = '')
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
        $o->setOptions($arr);
        $this->add($o, $text);
    }

    /** Adds radio buttons */
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
        $o->expanded_size = 10;
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

    /** Adds a YuiAutocomplete powered input field */
    function addAutocomplete($name, $text, $url, $rf, $js = '')
    {
        $o = new YuiAutocomplete();
        $o->setName($name);
        $o->setXhrUrl($url);
        $o->setResultFields($rf);
        $o->setJsFormatResult($js);
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

    /** Adds a recaptcha */
    function addRecaptcha()
    {
        $this->using_captcha = true;

        $o = new Recaptcha();
        $this->add($o);
    }

    /** Renders the form in XHTML */
    function render()
    {
//        if (!function_exists($this->post_handler) && !$this->js_onsubmit)
  //          throw new \Exception ('FATAL: XhtmlForm no post handler or js handler set');

        if (!$this->name)
            throw new \Exception ('need a form name');

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
        (!$this->autocomplete ? ' autocomplete="off"' : '').
        ($this->id          ? ' id="'.$this->id.'"' : '').
        ($this->title       ? ' title="'.$this->title.'"' : '').
        ($this->file_upload ? ' enctype="multipart/form-data"' : '').
        ($this->js_onsubmit ? ' onsubmit="'.$this->js_onsubmit.'"' : '').
        '>'.
        '<table'.
        ' style="padding: 6px;'.$this->css_table.'"'.
        '>';

        $hidden = '';

        // fills in form with previous entered data        XXXXX merge some code with handle()
        foreach ($this->elems as $e)
        {
            if (!isset($e['obj']))
                throw new \Exception ('ehjohohohoh: '.$e['obj']);

            if ( !($e['obj'] instanceof XhtmlComponent) )
                throw new \Exception ('obj not a XhtmlComponent');

            if ( $e['obj2'] && !($e['obj2'] instanceof XhtmlComponent) )
                throw new \Exception ('obj2 not a XhtmlComponent');

            if (isset($e['obj']->value))
                $e['obj']->value = htmlspecialchars($e['obj']->value);

            if ($e['obj'] instanceof XhtmlComponentHidden) {
                $hidden .= $e['obj']->render();
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
            $e['obj']->render().
            ($e['obj2'] instanceof XhtmlComponent ? $e['obj2']->render() : '').
            '</td>'.
            '</tr>';
        }

        $res .=
        '</table>'.
        $hidden.
        '</form>';

        return $res;
    }

}
