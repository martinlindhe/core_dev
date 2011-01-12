<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

///XXXX: make interface IXhtmlComponent instead?

require_once('XhtmlComponentInput.php');
require_once('XhtmlComponentSubmit.php');
require_once('XhtmlComponentOpenSearch.php');

abstract class XhtmlComponent
{
    var $name;

    abstract function render();
}

?>
