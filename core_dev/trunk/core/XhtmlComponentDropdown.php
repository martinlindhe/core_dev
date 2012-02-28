<?php
/**
 * $Id$
 *
 * Creates a select-dropdown list from a indexed array
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

//STATUS: ok

require_once('XhtmlComponent.php');

class XhtmlComponentDropdown extends XhtmlComponent
{
    var        $value;               ///< default value
    protected $options = array();  ///<  array of available options
    protected $js_onchange = '';

    function setOptions($a)
    {
        foreach ($a as $o)
            $this->options[] = htmlentities($o);
    }

    function setJsOnChange($s) { $this->js_onchange = $s; }

    function render()
    {
        if (!is_array($this->options))
            throw new Exception ('options not an array: '.$this->options);

        $out =
        '<select '.
            'name="'.strip_tags($this->name).'"'.
            ($this->js_onchange ? ' onchange="'.$this->js_onchange.'"' : '').
        '>'.
        '<option value="0">---</option>';    //default to "0" instead of an empty string for "no option selected"

        foreach ($this->options as $id => $title)
            $out .= '<option value="'.$id.'"'.($this->value == $id ? ' selected="selected"':'').'>'.$title.'</option>';

        $out .= '</select>';

        return $out;
    }

}

?>
