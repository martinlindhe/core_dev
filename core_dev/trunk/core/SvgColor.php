<?php
/**
 * $Id$
 *
 * http://www.w3.org/TR/SVG/types.html#DataTypeColor
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//FIXME: opacity is not quite correct ????

namespace cd;

class SvgColor
{
    var $r, $g, $b;

    function __construct($r = '', $g = '', $b = '')
    {
        if (!$r && !$g && !$b)
            return;

        if ($r && is_numeric($r) && !$g && !$b) {
            // tread numbers as decimal representation of hex color code
            $this->set( dechex($r) );
            return;
        }

        if ($r && substr($r, 0, 1) == '#' && !$g && !$b) {
            //#ff00ff  = rr,gg,bb in hex
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

        if (strlen($s) == 6) {
            $r = substr($s, 0, 2);
            $g = substr($s, 2, 2);
            $b = substr($s, 4, 2);
        } else if (strlen($s) == 3) {
            // #abc = #aabbcc (replicate each digit)
            $r = substr($s, 0, 1);
            $g = substr($s, 1, 1);
            $b = substr($s, 2, 1);
            $r = $r.$r;
            $g = $g.$g;
            $b = $b.$b;
        } else
            throw new Exception ('wierd length of color '.strlen($s));

        $this->r = hexdec($r);
        $this->g = hexdec($g);
        $this->b = hexdec($b);
    }

    function render()
    {
        return 'rgb('.$this->r.','.$this->g.','.$this->b.')';
    }

}

?>
