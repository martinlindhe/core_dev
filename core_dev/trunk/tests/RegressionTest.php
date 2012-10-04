<?php
/**
 * $Id$
 */

//STATUS: wip

namespace cd;

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

            if ($err !== $a[1]) {
                echo basename($filename).' FAIL '.$cnt.': '.$a[0].'. expected "'.$a[1].'" ('.gettype($a[0]).') but got "'.$err.'" ('.gettype($a[1]).")\n";
                $fail++;
            }

        }

        echo basename($filename).': '.($cnt-$fail).'/'.$cnt." SUCCESS\n";
    }

}

?>
