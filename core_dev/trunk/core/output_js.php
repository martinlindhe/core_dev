<?php
/**
 * $Id$
 *
 * Helper functions for JavaScript generation
 *
  * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

/**
 * used internally by jsArray1D, jsArray2D
 */
function jsArrayFlat($list, $with_keys)
{
    $res = '';
    foreach ($list as $key => $val)
    {
        $res .= ($with_keys ? $key.':' : '');
        if (is_bool($val)) $res .= ($val ? '1' : '0');
        else if (is_numeric($val)) $res .= $val;
        else {
            $val = str_replace('"', '&quot;', $val); //XXX cannot contain "
            $res .= '"'.$val.'"';
        }
        $res .= ',';
    }
    return $res;
}

/**
 * @param $list    array(key1=>val1, key2=>val2)
 * @return ["val1","val2",]   or  [key1:"val1",key2:"val2",]
 */
function jsArray1D($list, $with_keys = true)
{
    return '['.jsArrayFlat($list, $with_keys).']';
}

/**
 * Generates Javascript arrays
 * @param $list 2d array
 */
function jsArray2D($list)
{
    $res = '['."\n";

    foreach ($list as $l)
        $res .= '{ '.jsArrayFlat($l, true).'},'."\n";

    $res .= ']';

    return $res;
}

/**
 * @param $ms reload time in milliseconds (1/1000th second)
 */
function jsReload($ms)
{
    if (!is_numeric($ms)) return false;

    $res =
    '<script type="text/javascript">'.
    'setTimeout("location.reload();", '.$ms.');'.
    '</script>';

    return $res;
}

?>
