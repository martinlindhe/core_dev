<?php
/**
 * $Id$
 *
 * Creates a xhtml textarea
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('XhtmlComponent.php');

class XhtmlComponentTextarea extends XhtmlComponent
{
    var $value;
    var $style;

    var $width, $height;  ///< specifies dimensions in pixels
    var $cols, $rows;     ///< .. in cols, rows

    function render()
    {
        $css =
            $this->style.
            ($this->width  ? 'width:'.$this->width.'px;' : '').
            ($this->height ? 'height:'.$this->height.'px;' : '');

        return '<textarea name="'.$this->name.'" id="'.$this->name.'"'.
            ($css ? ' style="'.$css.'"' : '').
            ($this->cols ? ' cols="'.$this->cols.'"' : '').
            ($this->rows ? ' rows="'.$this->rows.'"' : '').
            '>'.$this->value.'</textarea>';
    }

}

?>