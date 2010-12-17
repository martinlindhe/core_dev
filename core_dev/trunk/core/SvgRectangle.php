<?php
/**
 * $Id$
 *
 * http://www.w3.org/TR/SVG/shapes.html#RectElement
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: add rounded corners support

class SvgRectangle implements ISvgComponent
{
    var $x = 0, $y = 0;
    var $color;             ///< optional border (SvgColor)
    var $fill_color;        ///< optional fill (SvgColor)
    var $width, $height;
    var $border_width = 2;

    function render()
    {
        $res =
        '<rect x="'.$this->x.'" y="'.$this->y.'" width="'.$this->width.'" height="'.$this->height.'"'.
        ' fill="'.($this->fill_color ? $this->fill_color->render() : 'none').'"'.
        ($this->color ? ' stroke="'.$this->color->render().'" stroke-width="'.$this->border_width.'"' : '').
        '/>';

        return $res;
    }
}

?>
