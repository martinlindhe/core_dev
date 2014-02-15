<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2012 <martin@ubique.se>
 */

namespace cd;

class CalendarEvent
{
    protected $date;
    var $title;
    var $timezone;

    function setDate($s) { $this->date = ts($s); }
    function getDate() { return $this->date;}

    function setTitle($s) { $this->title = $s; }
    function getTitle() { return $this->title; }

}

?>
