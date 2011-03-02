<?php
/**
 * $Id$
 *
 * Utility functions for bit manipulation
 */

/** Returns hi and low 4 bit pairs of a byte */
function byte_split($b)
{
    if (!intval($b) || $b > 255 || $b < 0)
        throw new Exception ('bad input');

    $lo = $b & 0xF;
    $hi = $b >> 4;
    return array($hi, $lo);
}

?>
