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
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

require_once('ConvertBase.php');

class ConvertDatasize extends ConvertBase
{
    protected $scale = array( ///< unit scale to a bit
    // http://en.wikipedia.org/wiki/Template:Quantities_of_bits
    'bit'  => 1,
    'kbit' => 1024,                      // 2^10
    'mbit' => 1048576,                   // 2^20
    'gbit' => 1073741824,                // 2^30
    'tbit' => 1099511627776,             // 2^40
    'pbit' => 1125899906842624,          // 2^50
    'ebit' => 1152921504606846976,       // 2^60
    'zbit' => 1180591620717411303424,    // 2^70
    'ybit' => 1208925819614629174706176, // 2^80

    // http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
    'b'    => 8,
    'kb'   => 8192,                      // (2^10)*8
    'mb'   => 8388608,                   // (2^20)*8
    'gb'   => 8589934592,                // (2^30)*8
    'tb'   => 8796093022208,             // (2^40)*8
    'pb'   => 9007199254740992,          // (2^50)*8
    'eb'   => 9223372036854775808,       // (2^60)*8
    'zb'   => 9444732965739290427392,    // (2^70)*8
    'yb'   => 9671406556917033397649408, // (2^80)*8
    );

    protected $lookup = array(
    // bit counts
    'bit'      => 'bit',  'bits'      => 'bit',
    'kilobit'  => 'kbit', 'kilobits'  => 'kbit',
    'megabit'  => 'mbit', 'megabits'  => 'mbit',
    'gigabit'  => 'gbit', 'gigabits'  => 'gbit',
    'terabit'  => 'tbit', 'terabits'  => 'tbit',
    'petabit'  => 'pbit', 'petabits'  => 'pbit',
    'exabit'   => 'ebit', 'exabits'   => 'ebit',
    'zettabit' => 'zbit', 'zettabits' => 'zbit',
    'yottabit' => 'ybit', 'yottabits' => 'ybit',

    // byte counts
    'byte'      => 'b',
    'kilobyte'  => 'kb', 'kbyte' => 'kb', 'kib' => 'kb', 'k' => 'kb',
    'megabyte'  => 'mb', 'mbyte' => 'mb', 'mib' => 'mb', 'm' => 'mb',
    'gigabyte'  => 'gb', 'gbyte' => 'gb', 'gib' => 'gb', 'g' => 'gb',
    'terabyte'  => 'tb', 'tbyte' => 'tb', 'tib' => 'tb', 't' => 'tb',
    'petabyte'  => 'pb',
    'exabyte'   => 'eb',
    'zettabyte' => 'zb',
    'yottabyte' => 'yb',
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
