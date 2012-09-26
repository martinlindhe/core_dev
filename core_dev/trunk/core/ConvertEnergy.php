<?php
/**
 * $Id$
 *
 * Conversion functions between different units of energy
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Energy
 * http://en.wikipedia.org/wiki/Unit_of_energy
 * http://en.wikipedia.org/wiki/Kilowatt_hour
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertEnergy extends ConvertBase
{
    protected $scale = array( ///< unit scale to watt hour
    'microwatt_h' => 0,000001,         // microwatt hour 10^-6
    'milliwatt_h' => 0,001,            // milliwatt hour 10^-3
    'Wh'          => 1,                // watt hour
    'kilowatt_h'  => 1000,             // kilowatt hour, 10^3
    'megawatt_h'  => 1000000,          // megawatt hour  10^6
    'gigawatt_h'  => 1000000000,       // gigawatt hour  10^9
    'terawatt_h'  => 1000000000000,    // terawatt hour  10^12
    'petawatt_h'  => 1000000000000000, // petawatt hour  10^15
    );

    protected $lookup = array(
    'µWh'   => 'microwatt_h',
    'mWh'   => 'milliwatt_h',
    'kWh'   => 'kilowatt_h',
    'MWh'   => 'megawatt_h',
    'GWh'   => 'gigawatt_h',
    'TWh'   => 'terawatt_h',
    'PWh'   => 'petawatt_h',
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

    function convLiteral($s, $to, $from = 'gram')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
