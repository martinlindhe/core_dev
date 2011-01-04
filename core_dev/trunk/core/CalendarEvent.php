<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

class CalendarEvent
{
    protected $date;
    var $title;
    var $timezone;

    function setDate($s) { $this->date = ts($s); }
    function getDate() { return $this->date;}
}

?>
