<?php

namespace cd;

require_once('RandomNumberMersenneTwister.php');

class RandomNumber
{
    public static function get()
    {
        return RandomNumberMersenneTwister::get();
    }

    public static function getInRange($min, $max)
    {
        return RandomNumberMersenneTwister::getInRange($min, $max);
    }

}
