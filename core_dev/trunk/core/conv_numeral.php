<?php
/**
 * $Id$
 *
 * Converter between different numeral systems
 *
 * http://en.wikipedia.org/wiki/Radix
 * http://en.wikipedia.org/wiki/Numeral_system
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//TODO: handle bases above 10 (at least hex)

require_once('class.CoreConverter.php');

class ConvertNumeral extends CoreConverter
{
    private $scale = array( ///< unit scale to a second
    'bin' => 2,
    'oct' => 8,
    'dec' => 10,
    //'hex' => 16,
    );

    private $lookup = array(
    'binary'      => 'bin',
    'octal'       => 'oct',
    'decimal'     => 'dec',
    //'hexadecimal' => 'hex',
    );

    function getShortcode($name)
    {
        $name = strtolower($name);

        if (!empty($this->lookup[$name])) return $this->lookup[$name];
        if (array_search($name, $this->lookup)) return $name;
        return false;
    }

    function conv($from, $to, $val)
    {
        $from = $this->getShortcode($from);
        $to   = $this->getShortcode($to);
        if (!$from || !$to) return false;
        if (!is_numeric($val)) return false; //XXX assumes base 2 to 10

        $base_from = $this->scale[$from];
        $base_to   = $this->scale[$to];

        $res = $val % $base_to;
        $multiplier = $base_from;

        while (($val = intval($val / $base_to)) > 0)
        {
            $res += ($val % $base_to) * $multiplier;
            $multiplier *= $base_from;
        }

        return $res;
    }

}

?>
