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

require_once('class.CoreConverter.php');

class ConvertDuration extends CoreConverter
{
    protected $scale = array( ///< unit scale to a second
    'sec'  => 1,
    'min'  => 60,
    'hr'   => 3600,
    'dy'   => 86400,
    'week' => 604800,
    'mo'   => 2592000,  //30 days
    'yr'   => 31556952, //365.2425 days (gregorian year)
    );

    protected $lookup = array(
    'second' => 'sec',
    'minute' => 'min',
    'hour'   => 'hr',
    'day'    => 'dy',
    'month'  => 'mo',
    'year'   => 'yr',
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

}

?>
