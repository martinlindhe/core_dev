<?php
/**
 * @author Martin Lindhe, 2007-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class XhtmlComponentText extends XhtmlComponent
{
    var $value;

    function render()
    {
        return $this->value;
    }

}
