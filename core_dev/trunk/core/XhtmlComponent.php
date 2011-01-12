<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

///XXXX: make interface IXhtmlComponent instead?

abstract class XhtmlComponent
{
    var $name;

    abstract function render();
}

?>
