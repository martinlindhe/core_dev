<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011-2012 <martin@startwars.org>
 */

//STATUS: wip

/*
 COMPATIBLITY, 2011-09-20:
  audio/wav   - works in FF6, Chrome14, Safari, Opera
  audio/mp3   - works in IE9, Chrome14, Safari, NOT IN FF6
  audio/ogg   - works in FF6, Chrome14, Safari, Opera
  audio/x-m4a - works in Chrome14, NOT IN FF6, ??? in IE9
  "webm audio" - XXXX test
*/

namespace cd;

require_once('XhtmlComponent.php');

class AudioComponent
{
    var $src;
    var $mimetype = '';
}

class XhtmlComponentAudio extends XhtmlComponent
{
    var $src;
    var $preload = 'auto'; ///< "auto", "metadata" or "none"
    private $components = array();

    function addComponent($src, $mimetype = '')
    {
        $c = new AudioComponent();
        $c->src = $src;
        $c->mimetype = $mimetype;

        $this->components[] = $c;
    }

    function render()
    {
        $res =
        '<audio controls="controls" preload="'.$this->preload.'">';

        foreach ($this->components as $c)
            $res .= '<source src="'.$c->src.'"'.($c->mimetype ? ' type="'.$c->mimetype.'"' : '').'/>';

        $res .=
            'Your browser does not support the audio element.'.
        '</audio>';

        return $res;
    }

}

?>
