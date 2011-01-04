<?php
/**
 * $Id$
 *
 * XHTML submit button
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: implement IXmlComponent

class XhtmlSubmit
{
    var $title = 'Submit';
    var $class = 'button';
    var $style;

    function render()
    {
        return
        '<input type="submit"'.
        ' value="'.t($this->title).'"'.
        ($this->class ? ' class="'.$this->class.'"' : '').
        ($this->style ? ' style="'.$this->style.'"' : '').
        '/>';
    }

}

?>
