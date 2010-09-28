<?php
/**
 * $Id$
 *
 * Conversion functions between different byte representations
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Units_of_information
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('ConvertBase.php');

class ConvertDatasize extends ConvertBase
{
    protected $scale = array( ///< unit scale to a bit
    'bit'  => 1,
    'kbit' => 1024,       // 2^10
    'mbit' => 1048576,    // 2^20
    'gbit' => 1073741824, // 2^30

    'b'    => 8,
    'kb'   => 8192,            // (2^10)*8
    'mb'   => 8388608,         // (2^20)*8
    'gb'   => 8589934592,      // (2^30)*8
    'tb'   => 8796093022208,   // (2^40)*8
    'pb'   => 9007199254740992,// (2^50)*8
    );

    protected $lookup = array(
    'bit'      => 'bit',
    'kilobit'  => 'kbit',
    'megabit'  => 'mbit',
    'gigabit'  => 'gbit',

    'byte'     => 'b',
    'kilobyte' => 'kb', 'kib' => 'kb', 'k' => 'kb',
    'megabyte' => 'mb', 'mib' => 'mb', 'm' => 'mb',
    'gigabyte' => 'gb', 'gib' => 'gb', 'g' => 'gb',
    'terabyte' => 'tb', 'tib' => 'tb', 't' => 'tb',
    'petabyte' => 'pb',
    );

    /**
     * @param $from conversion from unit
     * @param $to conversion to unit
     * @param $val value to convert
     * @return converted value
     */
    function conv($from, $to, $val)
    {
        $from = $this->getShortcode($from);
        $to   = $this->getShortcode($to);

        if (!$from || !$to)
            return false;

        $res = ($val * $this->scale[$from]) / $this->scale[$to];

        if ($this->precision)
            return round($res, $this->precision);

        return $res;
    }

    function convLiteral($s, $to, $from = 'byte')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
