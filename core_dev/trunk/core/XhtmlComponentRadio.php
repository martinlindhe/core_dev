<?php
/**
 * $Id$
 *
 * Creates radio buttons from a indexed array
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentRadio extends XhtmlComponent
{
    var $value;                   ///< default value
    var $options;                 ///<  array of available options
    protected $js_onchange = '';

    function setJsOnChange($s) { $this->js_onchange = $s; }

    function render()
    {
        if (!is_array($this->options))
            throw new Exception ('options not an array: '.$this->options);

        $out = '';

        foreach ($this->options as $id => $title)
        {
            $out .= '<input type="radio" class="radio" name="'.$this->name.'" value="'.$id.'" id="lab_'.$id.'"'.($this->value == $id ? ' checked="checked"' : '').'/>';
            $out .= '<label for="lab_'.$id.'"> '.$title.'</label><br/>';
        }

        return $out;
    }

}

?>
