<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class SvgColor
{
    var $r, $g, $b;

    function __construct($r = '', $g = '', $b = '')
    {
        if (!$r && !$g && !$b)
            return;

        if ($r && substr($r, 0, 1) == '#' && !$g && !$b) {
            $this->set($r);
            return;
        }

        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    function set($s)
    {
        if (substr($s, 0, 1) == '#')
            $s = substr($s, 1);

        if (strlen($s) != 6)
            throw new Exception ('wierd length of color '.strlen($s));

        $this->r = hexdec(substr($s, 0, 2));
        $this->g = hexdec(substr($s, 2, 2));
        $this->b = hexdec(substr($s, 4, 2));
    }

    function render()
    {
        return 'rgb('.$this->r.','.$this->g.','.$this->b.')';
    }
}

?>
