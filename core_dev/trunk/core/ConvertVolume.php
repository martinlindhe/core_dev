<?php
/**
 * $Id$
 *
 * Conversion functions between different units of volume
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Volume
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//TODO: add more units

require_once('ConvertBase.php');

class ConvertVolume extends ConvertBase
{
    protected $scale = array( ///< unit scale to 1 liter
    'l'         => 1,           // liter / litre
    'm³'        => 1000,        // cubic metre = 1000 litres
    'us_gallon' => 3.785411784, // us liquid gallon
    'uk_gallon' => 4.54609,     // imperial (uk) gallon
    );

    protected $lookup = array(
    'cubic meter' => 'm³',        'cubic meters'  => 'm³',    'cubic metre' => 'm³',  'cubic metres' => 'm³',
    'liter'       => 'l',         'liters'        => 'l',     'litre'       => 'l',   'litres'       => 'l',
    'gallon'      => 'us_gallon', 'gallons'       => 'us_gallon',
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

    function convLiteral($s, $to, $from = 'liter')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
