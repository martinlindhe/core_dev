<?php

//STATUS: wip

//TODO: implement IXmlComponent

class XhtmlInput
{
    var $name;
    var $value;           ///< default value
    var $size;
    var $maxlen;
    var $disabled = false;  ///< disable field (make it read only)

    function render()
    {
        $res =
        '<input type="text" name="'.$this->name.'" id="'.$this->name.'"'.
        (($this->value || is_string($this->value)) ? ' value="'.$this->value.'"' : '').
        ($this->size ? ' size="'.$this->size.'"': '').
        ($this->maxlen ? ' maxlength="'.$this->maxlen.'"': '').
        ($this->disabled ? ' disabled': '').
        '/>';

        return $res;
    }

}

?>
