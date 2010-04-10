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
    protected $scale = array( ///< digits in the numeral system
    'bin' => 2,
    'oct' => 8,
    'dec' => 10,
    //'hex' => 16,
    );

    protected $lookup = array(
    'binary'      => 'bin',
    'octal'       => 'oct',
    'decimal'     => 'dec',
    //'hexadecimal' => 'hex',
    );

    function conv($from, $to, $val)
    {
        $from = $this->getShortcode($from);
        $to   = $this->getShortcode($to);

        if (!$from || !$to)
            return false;

        //XXX assumes base 2 to 10
        if (!is_numeric($val))
            return false;

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
