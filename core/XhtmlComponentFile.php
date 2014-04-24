<?php
/**
 * XHTML file upload field
 *
 * @author Martin Lindhe, 2007-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class XhtmlComponentFile extends XhtmlComponent
{
    var $type;  // holder for the file type

    function render()
    {
        return '<input type="file" name="'.$this->name.'"/>';
    }

}
