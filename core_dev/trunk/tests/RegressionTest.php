<?php
/**
 * $Id$
 */

//STATUS: early wip

class RegressionTest
{
    static function check($arr)
    {
        $cnt = 0;
        $fail = 0;

        foreach ($arr as $a)
        {
            $cnt++;

            $code = 'return '.$a.';';

            $err = eval($code);

            if ($err) {
                echo 'FAIL '.$cnt.': '.$a."\n";
                $fail++;
            }

        }

        echo ($cnt-$fail).'/'.$cnt." SUCCESS\n";
    }

}

?>
