<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class TimeMeasure
{
    private $time_start;
    private $precision = 2;

    function __construct()
    {
        $this->time_start = microtime(true);
    }

    function setPrecision($n) { $this->precision = $n; }


    /**
     * @return elapsed time since the class was initiated
     */
    function getElapsedTime()
    {
        $t = microtime(true) - $this->time_start;
        if ($this->precision)
            return round($t, $this->precision);

        return $t;
    }

    /**
     * @return average per item (items / elapsed time)
     */
    function getAverage($n)
    {
        return round($n / $this->getElapsedTime(), $this->precision);
    }

}

?>
