<?php
/**
 * $Id$
 *
 * Utility functions for bit manipulation
 */

/** Returns hi and low 4 bit pairs of a byte */
function byte_split($b)
{
    if (is_string($b))
        $b = ord($b);

    if (!is_numeric($b) || $b > 255 || $b < 0)
        throw new \Exception ('bad input: '.$b);

    return
        array(
        $b >> 4,  // hi bits
        $b & 0xF  // low bits
        );
}

/** Returns array with bit values of input byte */
function byte_to_bits($b)
{
    if (is_string($b))
        $b = ord($b);

    if (!is_numeric($b) || $b > 255 || $b < 0)
        throw new \Exception ('bad input: '.$b);

    $res = array();

    $bits = array(1, 2, 4, 8, 16, 32, 64, 128);

    for ($i = 7; $i >= 0; $i--)
        $res[$i] = ($b & $bits[$i]) ? 1 : 0;

    return $res;
}

?>
