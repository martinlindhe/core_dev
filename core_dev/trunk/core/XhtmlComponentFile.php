<?php
/**
 * $Id$
 *
 * XHTML file upload field
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentFile extends XhtmlComponent
{
    function render()
    {
        return '<input type="file" name="'.$this->name.'"/>';
    }

}

?>
