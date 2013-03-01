<?php
/**
 * Generates random numbers based on the libc algorithm
 */

namespace cd;

class RandomNumberLibc
{
    public static function get()
    {
        return rand();
    }

    public static function getInRange($min, $max)
    {
        // srand() is automatically called
        return rand($min, $max);
    }

}
