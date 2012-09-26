<?php
/**
 * $Id$
 *
 * Conversion functions between different units of area
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Area
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertArea extends ConvertBase
{
    protected $scale = array( ///< unit scale to square metre (m²)
//TODO: square millimetre
//TODO: square centimetre
    'm²'    => 1,              // square metre
    'a'     => 100,            // are (100 m²)
    'ha'    => 10000,          // hectare (10 000 m²)
    'km²'   => 1000000,        // square kilometre (100 hectares)

//TODO: square inch
    'sq ft' => 0.09290304,     // square foot
    'sq yd' => 0.83612736,     // square yard = 9 square feet
    'acre'  => 4046.8564224,   // = 4840 square yards = 43560 square feet
//TODO: square mile (U.S. mile)
    );

    protected $lookup = array(
    'square meter'     => 'm²',   'square meters'      => 'm²',    'square metre'     => 'm²',  'square metres'     => 'm²',
    'are'              => 'a',    'ares'               => 'a',
    'hectare'          => 'ha',   'hectares'           => 'ha',
    'square kilometer' => 'km²',  'square kilometers'  => 'km²',   'square kilometre' => 'km²', 'square kilometres' => 'km²',
    'acres'            => 'acre',
    'square foot'      => 'sq ft', 'square feet'       => 'sq ft',
    'square yard'      => 'sq yd', 'square yards'      => 'sq yd',
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

    function convLiteral($s, $to, $from = 'm²')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
