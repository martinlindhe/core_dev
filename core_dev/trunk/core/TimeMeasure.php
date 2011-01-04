<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

class TimeMeasure
{
    private $time_start;
    private $precision = 2;

    function __construct()
    {
        $this->time_start = microtime(true);
    }

    /**
     * @return elapsed time since the class was initiated
     */
    function getElapsedTime()
    {
        return round(microtime(true) - $this->time_start, $this->precision);
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
