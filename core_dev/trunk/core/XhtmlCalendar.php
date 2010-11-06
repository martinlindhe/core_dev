<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//XXX: fix auto-focusing on current day... dont use input field hack

//TODO: add a render2D() method that gives a normal calendar
//TODO: configurable starting day of week

class XhtmlCalendar
{
    private $year, $month;
    private $current_month = false; ///< if true, the displayed month is current month
    private $events = array();
    private $auto_focus = false;

    function __construct($year = '', $month = '')
    {
        if (!$year)  $year  = date('Y');
        if (!$month) $month = date('n');

        $this->setDate($year, $month);
    }

    function setDate($year, $month)
    {
        $this->year  = $year;
        $this->month = $month;

        $ts = mktime(0, 0, 0, $this->month, 1, $this->year);
        $this->days_in_month = date('t', $ts);

        //are we showing current month?
        $current_ts = mktime(0, 0, 0, date('n'), 1, date('Y'));
        if ($ts == $current_ts)
            $this->current_month = true;
    }

    function addEvent($date, $title)
    {
        $ts = ts($date);
        if (!$ts) throw new Exception('invalid date '.$date);

        $this->events[$ts][] = $title;
    }

    /** If true, auto focuses the calendar view on current day */
    function setAutoFocus($b)
    {
        $this->auto_focus = $b;
/*
        if ($this->current_month && $this->auto_focus) {
            $header = XhtmlHeader::getInstance();
            $header->embedJs("document.getElementById('cal_current_day').focus();");
        }
*/
    }

    function renderFlat()
    {
        $loc = LocaleHandler::getInstance();

        $res = '<h1>'.$loc->getMonthLong($this->month).' '.$this->year.'</h1>';
        for ($i=1; $i<=12; $i++)
            $res .= '<a href="'.$_SERVER['PHP_SELF'].'?year='.$this->year.'&month='.$i.'">'.$loc->getMonthShort($i).'</a> ';
        $res .= '<br/>';

        $res .= '<table border="1">';

        for ($i=1; $i<=$this->days_in_month; $i++) {
            $ts = mktime(0, 0, 0, $this->month, $i, $this->year);
            $weekday = date('w', $ts);

            $style = '';
            if ($weekday==0 || $weekday==6)
                $style = 'background-color:#aaa"';

            if ($i == date('j') && $this->current_month)
                $style = 'background-color:#77ee77"';

            $res .=
            '<tr style="'.$style.'">'.

                '<td valign="top" align="right">'.$i.'</td>'.
                '<td valign="top">'.$loc->getWeekdayLong( $weekday ).'</td>'.
                '<td>';

                    if ($i == date('j') && $this->current_month && $this->auto_focus)
                        $res .= '<a id="cal_current_day"></a>';

                    foreach ($this->events as $event_ts => $events)
                        if ($event_ts == $ts)
                            $res .= implode('<hr/>', $events);

            $res .=
                '</td>'.
            '</tr>';
        }

        $res .= '</table>';

        return $res;
    }
}

/*
$cal = new XhtmlCalendar(2010, 4);
$cal->addEvent('2010-04-14', 'fjortonde!');
$cal->addEvent('2010-04-14', 'fjortonde222!');
$cal->addEvent('2010-04-17', 'mer skit');
echo $cal->renderFlat();
*/

?>
