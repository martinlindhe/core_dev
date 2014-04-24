<?php
/**
 * XHTML submit button
 *
 * @author Martin Lindhe, 2007-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class XhtmlComponentSubmit extends XhtmlComponent
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
