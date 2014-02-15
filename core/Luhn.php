<?php
/**
 * $Id$
 *
 * To calculate the checksum, multiply the individual digits in the identity number with 212121-212.
 * The resulting products (a two digit product, such as 16, would be converted to 1 + 6) are
 * added together. The checksum is 10 minus the ones place digit in this sum.
 *
 * @author Martin Lindhe, 2007-2013 <martin@ubique.se>
 */

namespace cd;

class Luhn
{
    public static function Calculate($s)
    {
        $sum = 0;
        // echo "calc luhn: ".$s.".\n";

        $cnt = 0;
        for ($i = strlen($s)-1; $i >= 0; $i--)
        {
            // Switch between 212121212
            $tmp = substr($s, $i, 1) * (2 - ($cnt & 1));

            if ($tmp > 9)
                $tmp -= 9;

            // echo substr($s, $i, 1).' * '.(2 - ($cnt & 1))  . '  = '.$tmp;

            $cnt ++;
            $sum += $tmp;

            // echo " ( sum = ".$sum.")\n";
        }

        // Substract the ones place digit from 10
        $sum = (10 - ($sum % 10)) % 10;
        return $sum;
    }
}
