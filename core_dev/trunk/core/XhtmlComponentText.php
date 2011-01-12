<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentText extends XhtmlComponent
{
    var $value;

    function render()
    {
        return $this->value;
    }

}

?>
