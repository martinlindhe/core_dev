<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('XhtmlComponent.php');

class XhtmlComponentListbox extends XhtmlComponent
{
    var $value;                   ///< default value
    var $options;                 ///<  array of available options
    protected $js_onchange = '';

    function setJsOnChange($s) { $this->js_onchange = $s; }

    function render()
    {
        if (!is_array($this->options))
            throw new \Exception ('options not an array: '.$this->options);

        $page = XmlDocumentHandler::getInstance();
        $header = XhtmlHeader::getInstance();

        $el_id = 'multi_'.mt_rand();

        $header->registerJsFunction(
        'function toggle_multi_opt(n) {'.
            'var e = document.getElementById(n);'.
            'if (e.multiple == true) {'.
                'e.multiple = false;'.
            '} else {'.
                'e.multiple = true;'.
            '}'.
        '}'
        );

        $out = '<select id="'.$el_id.'" name="'.strip_tags($this->name).'[]"'.($this->js_onchange ? ' onchange="'.$this->js_onchange.'"' : '').'>';

        $out .= '<option value="0">---</option>';    //default to "0" instead of an empty string for "no option selected"

        foreach ($this->options as $id => $title)
            $out .= '<option value="'.$id.'"'.($this->value && $this->value == $id ? ' selected="selected"':'').'>'.$title.'</option>';

        $out .= '</select>';

        $out .= '<a href="#" onclick="toggle_multi_opt(\''.$el_id.'\'); return false;" style="vertical-align: bottom;"><img src="'.$page->getRelativeCoreDevUrl().'gfx/bullet_toggle_plus.png"/></a>';

        return $out;
    }

}

?>
