<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@ubique.se>
 */

//STATUS: wip, unused

namespace cd;

require_once('core.php');
require_once('CoreProperty.php');

class Version extends CoreProperty
{
    private $major;
    private $minor;
    private $micro;
    private $raw; ///< raw input

    private $type  = 0;

    const VERSION  = 1; //v1.5.390a
    const REVISION = 2; //r1234

    function get()
    {
        return $this->render();
    }

    function set($s)
    {
        if (!$s) return;

        $this->raw = $s;

        //XXX regexp match for "r123"
        $this->type = self::VERSION;

        //XXX assuming "1.2.3" format
        $x = explode('.', $s, 3);
        $this->major = $x[0];

        if (isset($x[1]))
            $this->minor = $x[1];

        if (isset($x[2]))
            $this->micro = $x[2];
    }

    function render()
    {
        switch ($this->type) {
        case self::VERSION:
            return $this->major.
                ($this->minor || $this->micro ? '.'.$this->minor : '').
                ($this->micro ? '.'.$this->micro : '');
        default:
            return 'Version: cant render "'.$this->raw.'"';
        }
    }

}

?>
