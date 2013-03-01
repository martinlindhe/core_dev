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

class DiceDnD
{
    var $numberOfDices = 0;
    var $numberOfDots  = 0;
    var $adjust        = 0;

    function __construct($cmd)
    {
        $pos = stripos($cmd, 'D');
        if ($pos === false)
            throw new \Exception ('wrong format: '.$cmd);

        $this->numberOfDices = intval(substr($cmd, 0, $pos));
        $s1 = substr($cmd, $pos+1);
        $this->numberOfDots  = $s1;

        $pos = strpos($s1, '+');
        if ($pos !== false) {
            $this->numberOfDots = intval(substr($s1, 0, $pos));
            $this->adjust       = intval(substr($s1, $pos+1));
        }

        $pos = strpos($s1, '-');
        if ($pos !== false) {
            $this->numberOfDots = intval(substr($s1, 0, $pos));
            $this->adjust       = -(intval( substr($s1, $pos+1) ));
        }
    }

    function roll()
    {
        $result = 0;

        for ($i = 0; $i < $this->numberOfDices; $i++)
            $result += (rand() % $this->numberOfDots) + 1;

        return $result + $this->adjust;
    }

    /** Calculates the best possible outcome of a dice roll */
    function max()
    {
        return ($this->numberOfDices * $this->numberOfDots) + $this->adjust;
    }

    /** Calculates the worst possible outcome of a dice roll */
    function min()
    {
        return $this->numberOfDices + $this->adjust;
    }

    function about()
    {
        return
            $this->numberOfDices.' dices with  '.$this->numberOfDots.' dots and '.
            $this->adjust.' adjustment, min '.$this->min().', max '.$this->max();
    }

}

