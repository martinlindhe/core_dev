<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

require_once('YuiRichedit.php');

class XhtmlComponentRichedit extends XhtmlComponent
{
    var $value;
    var $width;
    var $height;

    function render()
    {
        $hold = new XhtmlComponentTextarea();
        $hold->name   = $this->name;
        $hold->value  = $this->value;
        $hold->width  = 1;
        $hold->height = 1;

        $re = new YuiRichedit();
        $re->setInputName($this->name);
        $re->setWidth($this->width);
        $re->setHeight($this->height);

        return $hold->render().$re->render();
    }

}

?>
