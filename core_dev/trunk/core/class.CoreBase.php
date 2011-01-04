<?php
/**
 * $Id$
 *
 * Base class, all objects should extend from this class
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

class CoreBase
{
    private $debug = false;

    function getDebug() { return $this->debug; }
    function setDebug($bool = true) { $this->debug = $bool; }

    /** __set() is run when writing data to inaccessible properties. */
    public function __set($name, $value)
    {
        if (!isset($this->$name))
            throw new Exception ($name." property does not exist");
    }

}

?>
