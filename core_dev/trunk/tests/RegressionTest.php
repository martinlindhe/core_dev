<?php
/**
 * $Id$
 */

//STATUS: wip

class RegressionTest
{
    static function check($filename, $arr)
    {
        $cnt = 0;
        $fail = 0;

        foreach ($arr as $a)
        {
            $cnt++;


            $code = 'return '.$a[0].';';

            $err = eval($code);

            if ($err != $a[1]) {
                echo basename($filename).' FAIL '.$cnt.': '.$a[0].'. expected "'.$a[1].'" but got "'.$err.'"'."\n";
                $fail++;
            }

        }

        echo basename($filename).': '.($cnt-$fail).'/'.$cnt." SUCCESS\n";
    }

}

?>
