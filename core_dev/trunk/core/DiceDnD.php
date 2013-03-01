<?php
/**
 * Rolls dices, according to standard AD&D format:
 *
 * nDd([+/-]m)
 *
 * where
 * n - is the number of dices to roll
 * d - is the number of dots on each dice
 * m - modifier
 * and optional modifier as a signed integer
 *
 * Example:
 * 2D6+2 (roll 2 six sided dices and add 2 to the result)
 */

namespace cd;

require_once('RandomNumber.php');

class WrongFormatException extends \Exception { }

class DiceDnD
{
    var $numberOfDices = 0;
    var $numberOfDots  = 0;
    var $adjustment    = 0;

    function __construct($cmd)
    {
        $pos = stripos($cmd, 'D');
        if ($pos === false)
            throw new WrongFormatException();

        $this->numberOfDices = intval(substr($cmd, 0, $pos));

        $s = substr($cmd, $pos+1);

        $pos = strpos($s, '+');
        if ($pos === false)
            $pos = strpos($s, '-');

        if ($pos === false) {
            $this->numberOfDots = $s;
            return;
        }

        $this->numberOfDots = intval(substr($s, 0, $pos));
        $this->adjustment   = intval(substr($s, $pos));
    }

    function roll()
    {
        $result = 0;

        for ($i = 0; $i < $this->numberOfDices; $i++)
            $result += (RandomNumber::get() % $this->numberOfDots) + 1;

        return $result + $this->adjustment;
    }

    function min()
    {
        return $this->numberOfDices + $this->adjustment;
    }

    function max()
    {
        return ($this->numberOfDices * $this->numberOfDots) + $this->adjustment;
    }

}

