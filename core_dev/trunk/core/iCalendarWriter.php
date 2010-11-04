<?php
/**
 * $Id$
 *
 * iCalendar (.ics) file functions
 *
 * References:
 * http://tools.ietf.org/html/rfc5545 - Internet Calendaring and Scheduling Core Object Specification (iCalendar)
 * http://tools.ietf.org/html/rfc5546 - iCalendar Transport-Independent Interoperability Protocol (iTIP)
 * http://tools.ietf.org/html/rfc2447 - iCalendar Message-Based Interoperability Protocol (iMIP)
 * http://en.wikipedia.org/wiki/ICalendar
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: come up with a elegant solution to store needed data for "days off" tables
//TODO: use daysOffSwe() in paydaysMonthly() to find out if assumed weekday really
//      is a weekday (for example you never get salary on 25:th december)
//TODO: verify that the calendars work with Apple Calendar & Google Calendar

class iCalendarWriter
{
    var $events     = array();
    var $dateevents = array();

    private $prod_id = 'core_dev';

    var $name;

    function __construct($name = '')
    {
        $this->name = $name;
    }

    function sendHeaders()
    {
        header('Content-Type: text/calendar; charset="UTF-8"');
        //header('Content-Disposition: inline; filename=calendar.ics');
        header('Cache-Control: no-cache, must-revalidate');   //HTTP/1.1
        header('Expires: Thu, 1 Jan 2009 00:00:00 GMT');      //date in the past
    }

    function render()
    {
        $res = '';

        foreach ($this->dateevents as $e) {
            $y = date('Y', $e[0]);
            $m = date('m', $e[0]);
            $d = date('d', $e[0]);
            $c_start = date("Ymd", $e[0]);
            $c_end   = date("Ymd", mktime(0, 0, 0, $m, $d +1 , $y));    //date+1

            $res .=
            $this->tagBegin('VEVENT').
            "DTSTART;VALUE=DATE:".$c_start."\r\n".    //YYYYMMDD
            "DTEND;VALUE=DATE:".  $c_end  ."\r\n".
            "SUMMARY:".$e[1]."\r\n".
            "UID:".md5($c_start.$c_end.$e[1])."@".$this->prod_id."r\n". //unique identifier
            $this->tagEnd('VEVENT');
        }

        foreach ($this->events as $e) {    //XXX currently unused
            $tz = $e[1];
            $c_start = ($tz?$tz:date('e',$e[0][0])).":".date('Ymd', $e[0][0])."T000000";
            $c_end   = ($tz?$tz:date('e',$e[0][0])).":".date('Ymd', $e[0][0])."T235959";

            $res .=
            $this->tagBegin('VEVENT').
            "DTSTART;TZID=".$c_start."\r\n".    //XXX what is this dateformat called?
            "DTEND;TZID=".  $c_end.  "\r\n".
            "SUMMARY:".$e[0][1]."\r\n".
            "UID:".md5($c_start.$c_end.$e[0][1])."@".$this->prod_id."\r\n". //unique identifier
            $this->tagEnd('VEVENT');
        }

        return
        $this->tagBegin('VCALENDAR', $this->name).
        "UID:".md5($this->name)."@".$this->prod_id."\r\n". //unique identifier
        $res.
        $this->tagEnd('VCALENDAR');
    }

    /**
     * Creates iCalendar begin tag
     */
    function tagBegin($obj, $s = '')
    {
        $res = "BEGIN:".$obj."\r\n";

        switch ($obj) {
        case 'VCALENDAR':
            $res .=
            "VERSION:2.0\r\n".
            "PRODID:-//".$this->prod_id."//NONSGML v1.0//EN\r\n".
            "CALSCALE:GREGORIAN\r\n".  // http://en.wikipedia.org/wiki/Gregorian_calendar
            "METHOD:PUBLISH\r\n".      // XXX ??? snodde från googles kalender
            "X-WR-TIMEZONE:UTC\r\n".   // XXX ??? snodde från googles kalender
            "X-WR-CALDESC:".$s."\r\n"; // A description of the calendar
            break;

        case 'VEVENT':
            break;
        }
        return $res;
    }

    function tagEnd($obj)
    {
        return "END:".$obj."\r\n";
    }

    /**
     * Adds additional events to the calendar
     */
    function addEvents($cal, $tz = '')
    {
        foreach ($cal as $a) {
            $this->events[] = array($a, $tz);
        }
    }

    /**
     * Adds events that is valid a whole day
     */
    function addDateEvents($cal)
    {
        foreach ($cal as $a) {
            $this->dateevents[] = $a;
        }
    }

    /**
     * Generates swedish days off (workfree days)
     *
     * Calculations verified 2008.05.13
     *
     * Details (in swedish) here:
     * http://sv.wikipedia.org/wiki/Helgdag#Allm.C3.A4nna_helgdagar_i_Sverige
     */
    function daysOffSwe($year)
    {
        if (!is_numeric($year)) return false;

        $res = array();

        //Nyårsdagen: 1:a januari
        $ts = mktime(0, 0, 0, 1, 1, $year);
        $res[] = array($ts, 'Nyårsdagen');

        //Trettondag jul: 6:e januari
        $ts = mktime(0, 0, 0, 1, 6, $year);
        $res[] = array($ts, 'Trettondag jul');

        //Första maj: 1:a maj
        $ts = mktime(0, 0, 0, 5, 1, $year);
        $res[] = array($ts, 'Första maj');

        //Sveriges nationaldag: 6:e juni
        $ts = mktime(0, 0, 0, 6, 6, $year);
        $res[] = array($ts, 'Sveriges nationaldag');

        //Julafton: 24:e december
        $ts = mktime(0, 0, 0, 12, 24, $year);
        $res[] = array($ts, 'Julafton');

        //Juldagen: 25:e december
        $ts = mktime(0, 0, 0, 12, 25, $year);
        $res[] = array($ts, 'Juldagen');

        //Annandag jul: 26:e december
        $ts = mktime(0, 0, 0, 12, 26, $year);
        $res[] = array($ts, 'Annandag jul');

        //Nyårsafton: 31 december
        $ts = mktime(0, 0, 0, 12, 31, $year);
        $res[] = array($ts, 'Nyårsafton');

        $easter_ofs = easter_days($year, CAL_GREGORIAN);    //number of days after March 21 on which Easter falls

        //Långfredagen (rörlig): fredagen närmast före påskdagen
        $ts = mktime(0, 0, 0, 3, 21 + $easter_ofs - 2, $year);
        $res[] = array($ts, 'Långfredagen');

        //Påskafton (rörlig): dagen innan påskdagen
        $ts = mktime(0, 0, 0, 3, 21 + $easter_ofs - 1, $year);
        $res[] = array($ts, 'Påskafton');

        //Påskdagen (rörlig): söndagen närmast efter den fullmåne som infaller på eller närmast efter den 21 mars
        $ts = mktime(0, 0, 0, 3, 21 + $easter_ofs, $year);
        $res[] = array($ts, 'Påskdagen');

        //Annandag påsk (rörlig): dagen efter påskdagen. alltid måndag
        $ts = mktime(0, 0, 0, 3, 21 + $easter_ofs + 1, $year);
        $res[] = array($ts, 'Annandag påsk');

        //Kristi himmelfärdsdagen (rörlig): sjätte torsdagen efter påskdagen (39 dagar efter)
        $ts = mktime(0, 0, 0, 3, 21 + $easter_ofs + 39, $year);
        $res[] = array($ts, 'Kristi himmelfärdsdagen');

        //Pingsdagen (rörlig): sjunde söndagen efter påskdagen (49 dagar efter)
        $ts = mktime(0, 0, 0, 3, 21 + $easter_ofs + 49, $year);
        $res[] = array($ts, 'Pingstdagen');

        //Midsommardagen (rörlig): den lördag som infaller under tiden den 20-26 jun
        $ts = mktime(0, 0, 0, 6, 20, $year);    //20:e juni
        $dow = date('N', $ts);    //day of week. 1=monday,7=sunday
        $ts = mktime(0, 0, 0, 6, 20-$dow+6, $year);
        $res[] = array($ts, 'Midsommardagen');

        //Midsommarafton (rörlig): dagen innan midsommardagen
        $ts = mktime(0, 0, 0, 6, 20-$dow+5, $year);
        $res[] = array($ts, 'Midsommarafton');

        //Alla helgons dag (rörlig): den lördag som infaller under tiden den 31 oktober-6 november
        $ts = mktime(0, 0, 0, 10, 31, $year);    //31:a okt
        $dow = date('N', $ts);    //day of week. 1=monday,7=sunday
        $ts = mktime(0, 0, 0, 10, 31-$dow+6, $year);
        $res[] = array($ts, 'Alla helgons dag');

        return $res;
    }

    /**
     * Generates calendar events for given year
     * for paydays, which occur at $dom or the last weekday before
     *
     * @param $year year to generate paydays for
     * @param $dom day of month when salary is paid
     * @param $desc optional textfield describing the event
     */
    function paydaysMonthly($year, $dom, $desc = 'Salary')
    {
        $res = array();

        for ($m=1; $m <= 12; $m++) {
            $ts = mktime(0, 0, 0, $m, $dom, $year);
            $dow = date('N', $ts);    //day of week. 1=monday,7=sunday
            if ($dow > 5) //saturday or sunday
                $ts = mktime(0, 0, 0, $m, $dom-$dow+5, $year);    //friday selected week
            $res[] = array($ts, $desc);
        }
        return $res;
    }
}

?>
