<?php
/**
 * $Id$
 *
 * Conversion functions between different duration scales
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Time
 * http://en.wikipedia.org/wiki/Leap_year
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('ConvertBase.php');

class ConvertDuration extends ConvertBase
{
    protected $scale = array( ///< unit scale to a second
    'sec'  => 1,
    'min'  => 60,
    'hr'   => 3600,
    'dy'   => 86400,
    'week' => 604800,
    'mo'   => 2592000,  //30 days
    'yr'   => 31556952, //365.2425 days (gregorian year), modern more exact measurement
    'jyr'  => 31557600, //365.25 days (julian year), still used sometimes as a simple estimate of a "year"
    );

    protected $lookup = array(
    'second'      => 'sec', 'seconds' => 'sec',
    'minute'      => 'min', 'minutes' => 'min',
    'hour'        => 'hr',  'hours'   => 'hr',
    'day'         => 'dy',  'days'    => 'dy',
    'month'       => 'mo',  'months'  => 'mo',
    'year'        => 'yr',  'years'   => 'yr',  'gregorian year' => 'yr',
    'julian year' => 'jyr',
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

?>
