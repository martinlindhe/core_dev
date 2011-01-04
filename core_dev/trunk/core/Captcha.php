<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: wip

abstract class Captcha
{
    abstract function render();

    abstract function verify();
}

?>
