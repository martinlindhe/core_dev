<?php
/**
 * $Id$
 *
 * XHTML text input field
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('XhtmlComponent.php');

class XhtmlComponentInput extends XhtmlComponent
{
    var $id;                ///< xhtml component id
    var $value;             ///< default value
    var $size;              ///< for <input type="text"> the size attribute defines the number of characters that should be visible
    var $style;
    var $width;             ///< width in pixels
    var $maxlen;
    var $disabled = false;  ///< disable field (make it read only)
    var $onchange;          ///< js to execute on change event (when input field lost focus after user input)

    function onChange($s) { $this->onchange = $s; }

    function render()
    {
        $id = $this->id ? $this->id : $this->name;
        if (strpos($id, '[') !== false && strpos($id, ']') !== false)
            $id = '';

        if (!is_alphanumeric($id))
            throw new Exception ('no: '.$id );

        $style = $this->style . ($this->width ? 'width:'.$this->width.'px;': '');

        $res =
        '<input type="text"'.
        ' name="'.$this->name.'"'.
        ($id ? ' id="'.$id.'"' : '').
        ($style ? ' style="'.$style.'"' : '').
        (($this->value || is_string($this->value)) ? ' value="'.$this->value.'"' : '').
        ($this->size ? ' size="'.$this->size.'"': '').
        ($this->maxlen ? ' maxlength="'.$this->maxlen.'"': '').
        ($this->disabled ? ' disabled': '').
        ($this->onchange ? ' onchange="'.$this->onchange.'"': '').
        '/>';

        return $res;
    }

}

?>
