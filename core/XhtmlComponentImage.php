<?php
/**
 * @author Martin Lindhe, 2007-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

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
        ' alt="'.$this->alt.'"'.
        ($this->width  ? ' width="'.$this->width.'"' : '').
        ($this->height ? ' height="'.$this->height.'"' : '').
        ($this->title  ? ' title="'.$this->title.'"' : '').
        ($this->onclick ? ' onclick="'.$this->onclick.'"' : '').
        ($this->class  ? ' class="'.$this->class.'"' : '').
        ($this->style  ? ' style="'.$this->style.'"' : '').
        '/>';
    }

}
