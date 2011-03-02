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
        throw new Exception ('bad input: '.$b);

    return
        array(
        $b >> 4,  // hi bits
        $b & 0xF  // low bits
        );
}

?>
