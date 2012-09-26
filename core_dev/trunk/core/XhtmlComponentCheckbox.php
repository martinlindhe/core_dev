<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('XhtmlComponent.php');

class XhtmlComponentCheckbox extends XhtmlComponent
{
    var $value;            ///< value (to submit to server if checkbox is checked)
    var $checked = false;
    var $title;            ///< clickable text

    protected $js_onclick;

    function onClick($s) { $this->js_onclick = $s; }

    function render()
    {
        if (is_array($this->value))
            throw new exception ('dont use arrays');

        $res = '';

        if (!$this->js_onclick)
        {
            $o = new XhtmlComponentHidden();
            $o->name = $this->name;
            $o->value = 0;
            $res .= $o->render();
        }

        $res .=
        '<input type="checkbox"'.
        ' name="'.$this->name.'"'.
        ' value="'.urlencode($this->value).'"'.
        ' id="lab_'.$this->name.'"'.
        ($this->checked ? ' checked="checked"':'').
        ($this->js_onclick ? ' onclick="'.$this->js_onclick.'"' : '').
        '/>';

        if ($this->title)
            $res .= '<label for="lab_'.$this->name.'"> '.$this->title.'</label>';

        return $res;
    }

}

?>
