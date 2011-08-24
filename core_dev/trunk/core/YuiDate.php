<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI date selector (javascript)
 *
 * Documentation:
 * http://developer.yahoo.com/yui/calendar/
 * http://developer.yahoo.com/yui/docs/YAHOO.widget.CalendarGroup.html
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXXX: upgrade yo yui 3.4.0 calendar when its released

require_once('XhtmlComponent.php');

require_once('JSON.php');

class YuiDate extends XhtmlComponent
{
    private $start_weekday = 1; //0=sundays, 1=mondays
    private $selected_date;

    function setStartWeekday($n) { $this->start_weekday = $n; }

    function setSelection($date)
    {
        $this->selected_date = ts($date);
    }

    function render()
    {
        if (!$this->name)
            throw new Exception ('must set a name');

        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.9.0/build/calendar/assets/skins/sam/calendar.css');

        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.9.0/build/calendar/calendar-min.js');

        $locale = LocaleHandler::getInstance();

        $div_holder = 'yui_date_hold'.mt_rand();

        $res =
        'YAHOO.namespace("example.calendar");'.
        'YAHOO.example.calendar.init = function() {'.

            'var inTxt = YAHOO.util.Dom.get("'.$this->name.'");'.
            ($this->selected_date ? 'inTxt.value  = "'.sql_date($this->selected_date).'";' : '').

            'var cal = YAHOO.example.calendar.cal1 = new YAHOO.widget.Calendar("'.$div_holder.'");'.
            ($this->selected_date ?
                'cal.cfg.setProperty("selected", "'.js_date($this->selected_date).'");'.
                'cal.cfg.setProperty("pagedate", "'.date('n/Y', $this->selected_date).'");'
                : ''
            ).
            'cal.cfg.setProperty("start_weekday",'.$this->start_weekday.');'.
            'cal.cfg.setProperty("MONTHS_SHORT",'.   JSON::encode($locale->handle->month_short, false).');'.
            'cal.cfg.setProperty("MONTHS_LONG",'.    JSON::encode($locale->handle->month_long, false).');'.
            'cal.cfg.setProperty("WEEKDAYS_1CHAR",'. JSON::encode($locale->handle->weekday_1char, false).');'.
            'cal.cfg.setProperty("WEEKDAYS_SHORT",'. JSON::encode($locale->handle->weekday_short, false).');'.
            'cal.cfg.setProperty("WEEKDAYS_MEDIUM",'.JSON::encode($locale->handle->weekday_medium, false).');'.
            'cal.cfg.setProperty("WEEKDAYS_LONG",'.  JSON::encode($locale->handle->weekday_long, false).');'.

            'cal.selectEvent.subscribe(function() {'.
                'var dates = this.getSelectedDates();'.
                'var inDate = dates[0];'.
                'inTxt.value = inDate.getFullYear() + "-" + (inDate.getMonth() + 1) + "-" + inDate.getDate();'.
            '}, cal, true);'.

            'cal.render();'.
        '}'."\n".
        'YAHOO.util.Event.onDOMReady(YAHOO.example.calendar.init);';

        return
        '<div id="'.$div_holder.'"></div>'.
        '<div style="clear:both"></div>'.
        xhtmlInput($this->name).'<br/>'.
        js_embed($res);
    }

}

?>
