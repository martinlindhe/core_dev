<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentIframe extends XhtmlComponent
{
    var $src;
    var $width, $height;     ///< in pixels
    var $scrolling = 'yes';  ///< "yes", "no", "auto"
    var $border = 1;         ///< "0" or "1"

    function render()
    {
        return
        '<iframe'.
        ($this->name   ? ' name="'.$this->name.'"' : '').
        ($this->width  ? ' width="'.$this->width.'"' : '').
        ($this->height ? ' height="'.$this->height.'"' : '').
        ($this->scrolling ? ' scrolling="'.$this->scrolling.'"' : '').
        ' frameborder="'.$this->border.'"'.
        ' src="'.$this->src.'"'.
        '>'.
        '</iframe>';
    }

}

?>
