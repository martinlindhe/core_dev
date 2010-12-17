<?php
/**
 * $Id$
 *
 * http://www.w3.org/TR/SVG11/text.html
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class SvgText implements ISvgComponent
{
    var $x = 0, $y = 0;
    var $color;             ///< SvgColor
    var $text;
    var $font = 'verdana';
    var $size = 11;

    function __construct($s = '')
    {
        $this->text = $s;
    }

    function render()
    {
        if (!$this->color)
            $this->color = new SvgColor('#000000');

        $res =
        '<text x="'.$this->x.'" y="'.$this->y.'" font-family="'.$this->font.'" font-size="'.$this->size.'" fill="'.$this->color->render().'" >'.
        $this->text.
        '</text>';

        return $res;
    }
}

?>
