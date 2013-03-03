<?php
/**
 * $Id$
 *
 * Converter between different numeral systems
 *
 * https://en.wikipedia.org/wiki/List_of_numeral_systems
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

//STATUS: ok

namespace cd;

require_once('IConvert.php');

class ConvertNumeral implements IConvert
{
    protected static $scale = array( ///< digits in the numeral system
    'binary'      => 2,
    'octal'       => 8,
    'decimal'     => 10,
    'hexadecimal' => 16,
    'vigesimal'   => 20,
    );

    protected static $lookup = array(
    'bin'  => 'binary',
    'oct'  => 'octal',
    'dec'  => 'decimal',
    'hex'  => 'hexadecimal',
    );

    protected static function getScale($s)
    {
        if (!empty(self::$scale[$s]))
            return self::$scale[$s];

        throw new \Exception ('xx');
    }

    public static function convert($from, $to, $val)
    {
        if ($from == 'binary' && substr($val, 0, 1) == 'b')
            $val = substr($val, 1);

        if ($from == 'octal' && substr($val, 0, 1) == 'o')
            $val = substr($val, 1);

        if ($from == 'hexadecimal' && substr($val, 0, 1) == 'x')
            $val = hexadecimal($val, 1);

        if ($from == 'hexadecimal' && substr($val, 0, 2) == '0x')
            $val = substr($val, 2);

        $from_base = self::getScale($from);
        $to_base = self::getScale($to);

        return base_convert($val, $from_base, $to_base);
    }

}
