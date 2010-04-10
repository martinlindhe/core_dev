<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: ok

require_once('class.CoreBase.php');

abstract class CoreConverter extends CoreBase
{
    protected $precision = 0;   ///< if set, specifies rounding precision. if unset, return exact result

    function setPrecision($n) { $this->precision = $n; }

    function getUnitname($name)
    {
        $n = $this->getShortcode($name);
        if (!$n)
            return false;

        return array_search($n, $this->lookup);
    }

    function getShortcode($name)
    {
        $name = strtolower(trim($name));
        if (substr($name, -1) == 's')
            $name = substr($name, 0, -1);

        if (!empty($this->lookup[$name]))
            return $this->lookup[$name];

        if (array_search($name, $this->lookup))
            return $name;

        return false;
    }

}

?>
