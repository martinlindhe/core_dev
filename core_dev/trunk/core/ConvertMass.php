<?php
/**
 * $Id$
 *
 * Conversion functions between different units of mass
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Mass
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

namespace cd;

require_once('ConvertBase.php');

class ConvertMass extends ConvertBase
{
    protected $scale = array( ///< unit scale to Gram
    'g'  => 1,             //Gram
    'hg' => 100,           //Hectogram
    'kg' => 1000,          //Kilogram
    't'  => 1000000,       //Tonne
    'kt' => 1000000000,    //Kilotonne
    'mt' => 1000000000000, //Megatonne
    'oz' => 28.349523125,  //Ounce (1/16 lb)
    'lb' => 453.59237,     //Pound
    'st' => 6350.29318,    //Stone (14 lb)
    );

    protected $lookup = array(
    'gram'      => 'g',
    'hecto'     => 'hg', 'hectogram' => 'hg',
    'kilo'      => 'kg', 'kilogram'  => 'kg',
    'ton'       => 't',  'tonne'     => 't',  //"metric tonne"
    'kiloton'   => 'kt', 'kilotonne' => 'kt',
    'megaton'   => 'mt', 'megatonne' => 'mt',

    'ounce'     => 'oz',    'ounces' => 'oz',
    'pound'     => 'lb',    'pounds' => 'lb', 'lbs'  => 'lb',
    'stone'     => 'st',    'stones' => 'st',
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
