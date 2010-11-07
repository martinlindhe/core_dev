<?php

class CalendarEvent
{
    protected $date;
    var $title;
    var $timezone;

    function setDate($s) { $this->date = ts($s); }
    function getDate() { return $this->date;}
}

?>
