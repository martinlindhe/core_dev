<?php
/**
 * $Id$
 *
 * http://www.w3.org/TR/SVG11/text.html
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

///XXX: firefox 4 still dont implement various text transformations 2010-12-20, see https://developer.mozilla.org/En/SVG_in_Firefox
//    - for example vertical text orientation is not supported (supported by Google Chrome)

namespace cd;

class SvgText implements ISvgComponent
{
    var $x = 0, $y = 0;
    var $color;             ///< SvgColor
    var $text;
    var $font = 'verdana';
    var $size = 11;
    var $vertical = false;

    function __construct($s = '')
    {
        $this->text = $s;
    }

    function render()
    {
        if (!$this->color)
            $this->color = new SvgColor('#000');

        // proper vertical text orientation: style="writing-mode: tb; glyph-orientation-vertical: 0;"      XXX only works in google chrome
        // XXX we "support" it using transform instead to rotate the text 90 degrees

        $res =
        '<text'.($this->vertical ? ' transform="rotate(270, 90, 0)"': '').
            ' x="'.$this->x.'" y="'.$this->y.'" fill="'.$this->color->render().'"'.
            ' font-family="'.$this->font.'" font-size="'.$this->size.'">'.
            $this->text.
        '</text>';

        return $res;
    }
}

?>
