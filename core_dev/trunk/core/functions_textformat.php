<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

/**
 * Takes text input such as "128M" and returns bytes
 */
function decodeDataSize($s)
{
    $s = str_replace(' ', '', $s);

    //FIXME find first non-digit in a easier way
    for ($i=0; $i<strlen($s); $i++) {
        if (!is_numeric(substr($s, $i, 1))) break;
    }
    $suff = substr($s, $i);
    $val = substr($s, 0, $i);

    switch (strtolower($suff)) {
        case 'g':
        case 'gb':
        case 'gib':
            return $val * 1024 * 1024 * 1024;

        case 'm':
        case 'mb':
        case 'mib':
            return $val * 1024 * 1024;

        case 'k':
        case 'kb':
        case 'kib':
            return $val * 1024;

        default:
            echo "decodeDataSize(): unknown suffix '".$suff."'\n";
    }
}

/**
 * Returns a string like "2 KiB"
 */
function formatDataSize($bytes, $tooltip = false) //XXX use ConvertDatasize class
{
    $org_bytes = $bytes;
    $units = array('bytes', 'k', 'mb', 'gb', 'tb');

    foreach ($units as $unit)
    {
        if ($bytes < 1024) break;
        $bytes = round($bytes/1024, 1);
    }

    if ($tooltip)
        return '<span title="'.$org_bytes.' bytes" style="cursor:pointer">'.$bytes.' '.$unit.'</span>';

    return $bytes.' '.$unit;
}

/**
 * Useful for formatting money values
 */
function formatNumber($number)
{
    $decimal_mark = ',';
    $thousand_mark = ' ';

    //Formats integers with grouped thousands, example: 2005 => 2 005
    if (intval($number) == $number) return number_format($number, 0, $decimal_mark, $thousand_mark);

    //Formats floats with 2 decimals and grouped thousands, example: 2005.4791 => 2 005,48
    return number_format($number, 2, $decimal_mark, $thousand_mark);
}

/**
 * Trims and removes excess spaces, tabs, linefeeds from a string
 */
function normalizeString($s, $tokens = array("\r", "\n", "\t"))
{
    foreach ($tokens as $t)
        $s = str_replace($t, ' ', $s);

    $s = trim($s);

    do { //Remove chunks of whitespace
        $tmp = $s;
        $s = str_replace('  ', ' ', $s);
    } while ($s != $tmp);

    return $s;
}

?>
