<?php
/**
 * $Id$
 *
 * XhtmlComponent compilant wrapper for YuiRichedit class
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX useless... YuiRichedit can directly extend from XhtmlComponent!!!!!

require_once('XhtmlComponent.php');

require_once('YuiRichedit.php');

class XhtmlComponentRichedit extends XhtmlComponent
{
    var $value;
    var $width;
    var $height;

    function render()
    {
        $re = new YuiRichedit();
        $re->setName($this->name);
        $re->setValue($this->value);
        $re->setWidth($this->width);
        $re->setHeight($this->height);

        return $re->render();
    }

}

?>
