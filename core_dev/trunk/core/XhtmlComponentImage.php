<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentImage extends XhtmlComponent
{
    var $src;
    var $alt;
    var $title;
    var $width;
    var $height;
    var $onclick;
    var $class;
    var $style;

    function onClick($js) { $this->onclick = $js; }

    function render()
    {
        return
        '<img src="'.$this->src.'"'.
        ($this->width  ? ' width="'.$this->width.'" ' : '').
        ($this->height ? ' height="'.$this->height.'" ' : '').
        ($this->alt    ? ' alt="'.$this->alt.'" ' : '').
        ($this->title  ? ' title="'.$this->title.'" ' : '').
        ($this->onclick ? ' onclick="'.$this->onclick.'"' : '').
        ($this->class ? ' class="'.$this->class.'"' : '').
        ($this->style ? ' style="'.$this->style.'"' : '').
        '/>';
    }

}

?>