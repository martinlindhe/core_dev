<?php
/**
 * $Id$
 *
 * Hash algorithm interface
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

interface IHash
{
    public static function CalcFile($file);

    public static function CalcString($s);
}

?>
