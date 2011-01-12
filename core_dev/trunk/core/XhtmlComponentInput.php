<?php
/**
 * $Id$
 *
 * XHTML text input field
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');

class XhtmlComponentInput extends XhtmlComponent
{
    var $id;                ///< xhtml component id
    var $value;             ///< default value
    var $size;
    var $maxlen;
    var $disabled = false;  ///< disable field (make it read only)

    function render()
    {
        $id = $this->id ? $this->id : $this->name;
        if (strpos($id, '[') !== false && strpos($id, ']') !== false)
            $id = '';

        if (!is_alphanumeric($id))
            throw new Exception ('no: '.$id );

        $res =
        '<input type="text"'.
        ' name="'.$this->name.'"'.
        ($id ? ' id="'.$id.'"' : '').
        (($this->value || is_string($this->value)) ? ' value="'.$this->value.'"' : '').
        ($this->size ? ' size="'.$this->size.'"': '').
        ($this->maxlen ? ' maxlength="'.$this->maxlen.'"': '').
        ($this->disabled ? ' disabled': '').
        '/>';

        return $res;
    }

}

?>
