<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2012 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('CoreBase.php');

abstract class CoreProperty extends CoreBase
{
    /**
     * Initialize object to specified value
     *
     * @param $s string or numeric value
     */
    function __construct($s = '')
    {
        $this->set($s);
    }

    /**
     * Convert object representation to a string
     */
    function __toString()
    {
        return $this->get().'';       //XXX cp. '' evaluerar true eller javetinte nåt är fel
    }

    abstract function set($s);
    abstract function get();

}

?>
