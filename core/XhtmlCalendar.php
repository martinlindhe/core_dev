<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2012 <martin@ubique.se>
 */

//STATUS: wip

//XXX: fix auto-focusing on current day... dont use input field hack

//TODO: add a render2D() method that gives a normal calendar
//TODO: configurable starting day of week

namespace cd;

require_once('time.php');
require_once('LocaleHandler.php');
require_once('iCalendarWriter.php');
require_once('CalendarEvent.php');

class XhtmlCalendar
{
    protected $name;  ///< name of the calendar
    protected $events = array();
    protected $auto_focus = false;

    protected $date;
    protected $days_in_month;

    function __construct($name = '')
    {
        $this->name = $name;

        // auto initialize to current year & month
        $this->setDate( time() );
    }

    function setDate($date)
    {
        $this->date = ts(sql_date(ts($date))); ///XXX FIXME: helper function to round a timestamp to a datestamp

        $this->days_in_month = date('t', $this->date);
    }

    function addEvent($e)
    {
        if (!($e instanceof CalendarEvent))
            throw new \Exception ('cant handle type');

        $this->events[] = $e;
    }

    /** If true, auto focuses the calendar view on current day */
    function setAutoFocus($b)
    {
        $this->auto_focus = $b;
/*
        if ($this->auto_focus) {
            $header = XhtmlHeader::getInstance();
            $header->embedJsOnload("document.getElementById('cal_current_day').focus();");
        }
*/
    }

    /** Renders the calender as a iCalendar file */
    function renderIcs()
    {
        $cal = new iCalendarWriter($this->name);
        //$cal->setFilename('xxx-'.date('Y').'.ics');

        foreach ($this->events as $e)
            $cal->addEvent($e);

        return $cal->render();
    }

    function render()
    {
        $loc = LocaleHandler::getInstance();

        $res = '<table summary="" border="1">';

        for ($i=1; $i<=$this->days_in_month; $i++)
        {
            $ts = mktime(0, 0, 0, date('m', $this->date), $i, date('Y', $this->date));
            $weekday = date('w', $ts);

            $focus = false;

            if ($i == date('j') && date('m', $this->date) == date('m') && date('Y', $this->date) == date('Y')) {
                $res .= '<tr style="background-color:#77ee77">';
                $focus = true;
            } else if ($weekday==0 || $weekday==6) {
                // weekend (sat, sun)
                $res .= '<tr style="background-color:#aaa">';
            } else {
                $res .= '<tr>';
            }

            $res .=
            '<td valign="top" align="right">'.
            ($focus ? '<a id="cal_current_day"></a>' : '').$i.'</td>'.
            '<td valign="top">'.$loc->getWeekdayLong( $weekday ).'</td>'.
            '<td>';

            foreach ($this->events as $e)
                if ($e->getDate() == $ts)
                    $res .= $e->getTitle().'<br/>';

            $res .=
            '</td>'.
            '</tr>';
        }

        $res .= '</table>';

        return $res;
    }

}

?>
