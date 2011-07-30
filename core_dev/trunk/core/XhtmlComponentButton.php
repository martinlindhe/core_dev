<?php
/**
 * $Id$
 *
 * XHTML button
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentButton extends XhtmlComponent
{
    var $text = 'Click me';
    var $class = 'button';
    var $style;

    function render()
    {
        return
        '<button type="button"'.
        ($this->class ? ' class="'.$this->class.'"' : '').
        ($this->style ? ' style="'.$this->style.'"' : '').
        '>'.$this->text.'</button>';
    }

}

?>
