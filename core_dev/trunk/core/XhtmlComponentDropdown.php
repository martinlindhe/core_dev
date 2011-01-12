<?php
/**
 * $Id$
 *
 * Creates a select-dropdown list from a indexed array
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentDropdown extends XhtmlComponent
{
    var $selected;            ///<  selected item
    var $onchange = '';       ///< XXXX unused

    function render()
    {
        $out =
        '<select name="'.strip_tags($this->name).'"'.
        ($this->onchange ? ' onchange="'.$this->onchange.'"' : '').
        '>';

        $out .= '<option value="0">---</option>';    //default to "0" instead of an empty string for "no option selected"

        foreach ($this->value as $id => $title)
            $out .= '<option value="'.$id.'"'.($this->selected == $id ? ' selected="selected"':'').'>'.$title.'</option>';

        $out .= '</select>';

        return $out;
    }

}

?>
