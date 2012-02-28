<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: ok

abstract class ConvertBase
{
    protected $precision = 0;   ///< if set, specifies rounding precision. if unset, return exact result

    /**
     * Does the converter recognize this data type?
     *
     * @return (bool)
     */
    function recognizeType($s)
    {
        if (in_array($s, $this->lookup) || array_key_exists($s, $this->lookup))
            return true;

        if (!empty($this->scale[$s]))
            return true;

//        echo "not recognized: ".$s."<br/>";
        return false;
    }

    function setPrecision($n) { $this->precision = $n; }

    /**
     * @param $code unit name or shortcode
     * @return unit name for the short code
     */
    function getUnitname($s)
    {
        if (in_array($s, $this->lookup))
            if ( isset( $this->lookup[ $s ] ))
                return $this->lookup[ $s ];
            else
                return $s;

        if (array_key_exists($s, $this->lookup))
            return $s;

        if (!empty($this->scale[$s]))
            return $s;

        return false;
    }

    /**
     * @param $name unit name or shortcode
     * @return shortcode for the unit name
     */
    function getShortcode($s, $lcase = true)
    {
        if ($lcase)
            $s = strtolower(trim($s));
        else
            $s = strtoupper(trim($s));

        if (!$s)
            return false;

        if (!empty($this->lookup[$s]))
            return $this->lookup[$s];

        if (in_array($s, $this->lookup) || array_key_exists($s, $this->lookup))
            return $s;

        if (!empty($this->scale[$s]))
            return $s;

        throw new Exception (get_class($this).': unhandled unit: '.$s);
    }

    /**
     * Converts input string such as "128M", "0.5 liter" or "100 celcius" to given output unit
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
            //HACK: find first non-digit/non-separator. replace with a regexp
            for ($i=0; $i<strlen($s); $i++) {
                $c = substr($s, $i, 1);
                if (!is_numeric($c) && $c != '.' && $c != ',')
                    break;
            }

            $suff = substr($s, $i);
            $val  = substr($s, 0, $i);

            $from = $this->getShortcode($suff);
        }

        return $this->conv($from, $to, $val);
    }

}

?>
