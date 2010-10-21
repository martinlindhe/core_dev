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
    protected $scale = array( ///< unit scale to cubic metre (m³)
    'l'     => 0.001,          // litre
    'm³'    => 1,              // cubic metre = 1000 litres
    );

    protected $lookup = array(
    'cubic meter'      => 'm³',   'cubic meters'       => 'm³',    'cubic metre'      => 'm³',  'cubic metres'      => 'm³',
    'litre'            => 'l',    'litres'             => 'l',
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

    function convLiteral($s, $to, $from = 'm³')
    {
        return parent::convLiteral($s, $to, $from);
    }

}

?>
