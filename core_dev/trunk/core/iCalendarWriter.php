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
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: move paydaysMonthly() and daysOffSwe() to a child class who extend iCalendarWriter
//XXX: verifiera alla uträknade datum för svenska helgdagar
//TODO: come up with a elegant solution to store needed data for "days off" tables
//TODO: use daysOffSwe() in paydaysMonthly() to find out if assumed weekday really
//      is a weekday (for example you never get salary on 25:th december)

//TODO: verify that the calendar output works with Apple Calendar

require_once('UUID.php');

require_once('CalendarEvent.php');
require_once('functions_time.php'); // for ts()

class iCalendarWriter
{
    var     $events   = array();

    private $filename;                ///< name of output document to be sent in the http header to the client (.ics extension)

    private $prod_id  = 'core_dev.com';
    private $mimetype = 'text/plain'; ///< should be "text/calendar", but "text/plain" works and shows better in browser

    var     $name;
    private $timezone;
    private $desc;                    ///< calendar description

    function __construct($name = '')
    {
        $this->name = $name;
        $this->timezone = date_default_timezone_get();
    }

    function setFilename($s) { $this->filename = $s; }

    /** Adds additional events to the calendar */
    function addEvent($e)
    {
        if (!($e instanceof CalendarEvent))
            throw new Exception ('cant handle object type '.get_class($e));

        $this->events[] = $e;
    }

    function addEvents($arr)
    {
        foreach ($arr as $e)
            $this->addEvent($e);
    }

    private function sendHeaders()
    {
        // charset header MUST be sent
        header('Content-Type: '.$this->mimetype.'; charset="UTF-8"');

        if ($this->filename)
            header('Content-Disposition: inline; filename='.$this->filename);

        header('Cache-Control: no-cache, must-revalidate');   //HTTP/1.1
        header('Expires: Thu, 1 Jan 2009 00:00:00 GMT');      //date in the past
    }

    function render()
    {
        $this->sendHeaders();
        $res = '';

        foreach ($this->events as $e)
        {
            $y = date('Y', $e->getDate());
            $m = date('m', $e->getDate());
            $d = date('d', $e->getDate());
            $c_start = date("Ymd", $e->getDate());
            $c_end   = date("Ymd", mktime(0, 0, 0, $m, $d +1 , $y));    //date+1

            $title = strip_tags($e->title);

            $res .=
            $this->tagBegin('VEVENT').
            "DTSTART;VALUE=DATE:".$c_start."\r\n".    //YYYYMMDD
            "DTEND;VALUE=DATE:".  $c_end  ."\r\n".
            //DTSTAMP:20101104T165340Z   /// UTC time
            "CLASS:PUBLIC\r\n".     // XXX ??? snodde från googles kalender
            "SEQUENCE:1\r\n".       // XXX ??? snodde från googles kalender
            "STATUS:CONFIRMED\r\n". // XXX ??? snodde från googles kalender
            "TRANSP:OPAQUE\r\n".    // XXX ??? snodde från googles kalender
            "SUMMARY:".$title."\r\n".
            "UID:".md5($c_start.$c_end.$title)."@".$this->prod_id."\r\n". //unique identifier
            $this->tagEnd('VEVENT');
        }

/*  //events who lasts other period than a full day
    $tz = $e[1];
    $c_start = ($tz?$tz:date('e',$e[0][0])).":".date('Ymd', $e[0][0])."T000000";
    $c_end   = ($tz?$tz:date('e',$e[0][0])).":".date('Ymd', $e[0][0])."T235959";
    "DTSTART;TZID=".$c_start."\r\n".    //XXX what is this dateformat called?
    "DTEND;TZID=".  $c_end.  "\r\n".
*/
        return
        $this->tagBegin('VCALENDAR', $this->name).
        "UID:".md5($this->name)."@".$this->prod_id."\r\n". //unique identifier
        $res.
        $this->tagEnd('VCALENDAR');
    }

    /** Creates iCalendar begin tag */
    private function tagBegin($obj, $s = '')
    {
        $res = "BEGIN:".$obj."\r\n";

        switch ($obj) {
        case 'VCALENDAR':
            $uuid  = UUID::v5('7c7884bf-14f8-478a-ab0e-778a6ac1d437', $this->name);

            $res .=
            "VERSION:2.0\r\n".
            "PRODID:-//".$this->prod_id."//NONSGML v1.0//EN\r\n".
            "X-WR-CALNAME:".$s."\r\n".               // Calendar name
            ($this->desc ? "X-WR-CALDESC:".$this->desc."\r\n" : ''). // Calendar description
            "X-WR-TIMEZONE:".$this->timezone."\r\n". // Calendar timezone, like "Europe/Stockholm"
            "X-WR-RELCALID:".$uuid."\r\n".           // Calendar UUID v5
            "CALSCALE:GREGORIAN\r\n".                // http://en.wikipedia.org/wiki/Gregorian_calendar
            "METHOD:PUBLISH\r\n";                    // XXX ??? snodde från googles kalender
            break;

        case 'VEVENT':
            break;
        }
        return $res;
    }

    private function tagEnd($obj)
    {
        return "END:".$obj."\r\n";
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
        if (!is_numeric($year))
            return false;

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
        for ($i = 31; $i<= 37; $i++) {
            $ts = mktime(0, 0, 0, 10, $i, $year);
            $dow = date('N', $ts);    //day of week. 1=monday,7=sunday
            if ($dow == 6) break;
        }
        $res[] = array($ts, 'Alla helgons dag');

        $out = array();
        foreach ($res as $r) {
            $e = new CalendarEvent();
            $e->setDate($r[0]);
            $e->title = $r[1];
            $out[] = $e;
        }

        return $out;
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

            $e = new CalendarEvent();
            $e->setDate($ts);
            $e->title = $desc;
            $res[] = $e;
        }
        return $res;
    }
}

?>
