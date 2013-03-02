<?php
/**
 * $Id$
 *
 * Conversion functions between different duration scales
 *
 * http://en.wikipedia.org/wiki/Conversion_of_units#Time
 * http://en.wikipedia.org/wiki/Leap_year
 * http://en.wiktionary.org/wiki/kiloyear
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertDuration extends ConvertBase
{
    protected $scale = array( ///< unit scale to a second
    'zs'   => 0.000000000000000000001, // zeptosecond (10 ^-21 of a second)
    'as'   => 0.000000000000000001,    // attosecond (10 ^-18 of a second)
    'fs'   => 0.000000000000001,       // femtosecond (10 ^−15 of a second)
    'ps'   => 0.000000000001,          // picosecond (10 ^−12 of a second)
    'ns'   => 0.000000001,             // nanosecond (10 ^-9)
    'µs'   => 0.000001,                // microsecond (10 ^-6)
    'ms'   => 0.001,                   // millisecond (10 ^-3)
    'cs'   => 0.01,                    // centisecond (1 / 100)
    'ds'   => 0.1,                     // decisecond  (1 / 10)
    'sec'  => 1,
    'min'  => 60,                      // 60 sec
    'hr'   => 3600,                    // 60 minutes
    'dy'   => 86400,                   // 24 hours
    'wk'   => 604800,                  // 7 days
    'mo'   => 2592000,                 // 30 days
    'yr'   => 31556952,                // 365.2425 days (gregorian year), modern more exact measurement
    'jyr'  => 31557600,                // 365.25 days (julian year), still used sometimes as a simple estimate of a "year"
    'ky'   => 31556952000,             // kilo-year
    );

    protected $lookup = array(
    'zeptosecond' => 'zs',  'zeptoseconds' => 'zs',
    'attosecond'  => 'as',  'attoseconds'  => 'as',
    'femtosecond' => 'fs',  'femtoseconds' => 'fs',
    'picosecond'  => 'ps',  'picoseconds'  => 'ps',
    'nanosecond'  => 'ns',  'nanoseconds'  => 'ns',
    'microsecond' => 'µs',  'microseconds' => 'µs',
    'millisecond' => 'ms',  'milliseconds' => 'ms',
    'centisecond' => 'cs',  'centiseconds' => 'cs',
    'decisecond'  => 'ds',  'deciseconds'  => 'ds',
    'second'      => 'sec', 'seconds'      => 'sec',
    'minute'      => 'min', 'minutes'      => 'min',
    'hour'        => 'hr',  'hours'        => 'hr',
    'day'         => 'dy',  'days'         => 'dy',
    'week'        => 'wk',  'weeks'        => 'wk',
    'month'       => 'mo',  'months'       => 'mo',
    'year'        => 'yr',  'years'        => 'yr',
    'julian year' => 'jyr', 'gregorian year' => 'yr',
    'kyr'         => 'ky',  'kyrs'         => 'ky',
    );

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

    function convLiteral($s, $to, $from = 'second')
    {
        return parent::convLiteral($s, $to, $from);
    }

}
