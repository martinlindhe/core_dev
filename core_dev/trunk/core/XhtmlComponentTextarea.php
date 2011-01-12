<?php
/**
 * $Id$
 *
 * Creates a xhtml textarea
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentTextarea extends XhtmlComponent
{
    var $value;
    var $width;
    var $height;

    function render()
    {
        return '<textarea name="'.$this->name.'" id="'.$this->name.'"'.
            ($this->width ? ' cols="'.$this->width.'"' : '').
            ($this->height ? ' rows="'.$this->height.'"' : '').
            '>'.$this->value.'</textarea>';
    }

}

?>
