<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: ok

require_once('class.CoreBase.php');

abstract class ConvertBase extends CoreBase
{
    protected $precision = 0;   ///< if set, specifies rounding precision. if unset, return exact result

    function setPrecision($n) { $this->precision = $n; }

    /**
     * @param $code unit name or shortcode
     * @return unit name for the short code
     */
    function getUnitname($code)
    {
        $n = $this->getShortcode($code);
        if (!$n)
            return false;

        return array_search($n, $this->lookup);
    }

    /**
     * @param $name unit name or shortcode
     * @return shortcode for the unit name
     */
    function getShortcode($name)
    {
        $name = strtolower(trim($name));
        if (!$name)
            return false;

        if (!empty($this->lookup[$name]))
            return $this->lookup[$name];

        if (array_search($name, $this->lookup))
            return $name;

        throw new Exception (get_class($this).': unhandled unit: '.$name);
    }

    /**
     * Converts input string such as "128M" or "100 celcius" to given output unit
     *
     * @param $s literal datasize, such as "128M" or numeric
     * @param $to conversion to unit
     * @param $from standard unit for the scale (supplied by child class)
     * @return converted value
     */
    function convLiteral($s, $to, $from = '')
    {
        $to = $this->getShortcode($to);
        if (!$to)
            return false;

        if (is_numeric($s)) {
            $val = $s;
        } else {
            $s = str_replace(' ', '', $s);

            //HACK find first non-digit in a easier way
            for ($i=0; $i<strlen($s); $i++)
                if (!is_numeric(substr($s, $i, 1)))
                    break;

            $suff = substr($s, $i);
            $val  = substr($s, 0, $i);

            $from = $this->getShortcode($suff);
        }

        return $this->conv($from, $to, $val);
    }

}

?>
