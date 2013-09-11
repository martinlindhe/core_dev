<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2013 <martin@startwars.org>
 */

namespace cd;

interface IConvert
{
    /**
     * @param $from conversion from unit
     * @param $to conversion to unit
     * @param $val value to convert
     * @return converted value
     */
    public static function convert($from, $to, $val);

}
