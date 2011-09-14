<?php
/**
 * $Id$
 *
 * XHTML hidden input field
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentHidden extends XhtmlComponent
{
    var $value; ///<  field value   (XXXX or array of multiple values ???)

    function render()
    {
        if (is_array($this->value))
            throw new exception ('dont use arrays'); //XXX do any code exploit this "feature"?

/*
        if (is_array($this->value))
            foreach ($this->value as $v)
                $out .= '<input type="hidden" name="'.$this->name.'[]" value="'.$v.'"/>';
*/
        return
        '<input type="hidden"'.
        ' name="'.$this->name.'"'.
        ' value="'.htmlspecialchars($this->value).'"'.
        '/>';
    }

}

?>
