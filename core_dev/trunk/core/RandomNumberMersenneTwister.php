<?php
/**
 * Generates random numbers based on the Mersenne Twister algorithm
 *
 * https://en.wikipedia.org/wiki/Mersenne_Twister
 */

namespace cd;

class RandomNumberMersenneTwister
{
    public static function get()
    {
        return mt_rand();
    }

    public static function getInRange($min, $max)
    {
        // mt_srand() is automatically called
        return mt_rand($min, $max);
    }

}
