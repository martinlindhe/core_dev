<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI date interval selector (javascript)
 *
 * Based on:
 * http://developer.yahoo.com/yui/examples/calendar/intervalcal.html
 *
 * See also:
 * http://developer.yahoo.com/yui/calendar/
 * http://developer.yahoo.com/yui/docs/YAHOO.widget.CalendarGroup.html
 *
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class yui_dateinterval
{
    private $name_from = 'yui_dateinterval_from';
    private $name_to   = 'yui_dateinterval_to';
    private $div_name  = 'yui_dateinterval_holder';
    private $start_weekday = 1; //0=sundays, 1=mondays

    function setNameFrom($s) { $this->name_from = $s; }
    function setNameTo($s) { $this->name_to = $s; }
    function setDivName($s) { $this->div_name = $s; }
    function setStartWeekday($n) { $this->start_weekday = $n; }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/combo?2.8.0r4/build/calendar/assets/skins/sam/calendar.css');
        $header->includeJs('http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js&2.8.0r4/build/calendar/calendar-min.js');

        $res = '
        (function() {

            function IntervalCalendar(container, cfg) {
                this._iState = 0;

                cfg = cfg || {};
                cfg.multi_select = true;

                IntervalCalendar.superclass.constructor.call(this, container, cfg);

                this.beforeSelectEvent.subscribe(this._intervalOnBeforeSelect, this, true);
                this.selectEvent.subscribe(this._intervalOnSelect, this, true);
                this.beforeDeselectEvent.subscribe(this._intervalOnBeforeDeselect, this, true);
                this.deselectEvent.subscribe(this._intervalOnDeselect, this, true);
            }

            IntervalCalendar._DEFAULT_CONFIG = YAHOO.widget.CalendarGroup._DEFAULT_CONFIG;

            YAHOO.lang.extend(IntervalCalendar, YAHOO.widget.CalendarGroup, {

                _dateString : function(d) {
                    var a = [];
                    a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_MONTH_POSITION.key)-1] = (d.getMonth() + 1);
                    a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_DAY_POSITION.key)-1] = d.getDate();
                    a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_YEAR_POSITION.key)-1] = d.getFullYear();
                    var s = this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.DATE_FIELD_DELIMITER.key);
                    return a.join(s);
                },

                _dateIntervalString : function(l, u) {
                    var s = this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.DATE_RANGE_DELIMITER.key);
                    return (this._dateString(l)
                            + s + this._dateString(u));
                },

                getInterval : function() {
                    // Get selected dates
                    var dates = this.getSelectedDates();
                    if(dates.length > 0) {
                        // Return lower and upper date in array
                        var l = dates[0];
                        var u = dates[dates.length - 1];
                        return [l, u];
                    }
                    else {
                        // No dates selected, return empty array
                        return [];
                    }
                },

                setInterval : function(d1, d2) {
                    // Determine lower and upper dates
                    var b = (d1 <= d2);
                    var l = b ? d1 : d2;
                    var u = b ? d2 : d1;
                    // Update configuration
                    this.cfg.setProperty("selected", this._dateIntervalString(l, u), false);
                    this._iState = 2;
                },

                resetInterval : function() {
                    // Update configuration
                    this.cfg.setProperty("selected", [], false);
                    this._iState = 0;
                },

                _intervalOnBeforeSelect : function(t,a,o) {
                    // Update interval state
                    this._iState = (this._iState + 1) % 3;
                    if(this._iState == 0) {
                        // If starting over with upcoming selection, first deselect all
                        this.deselectAll();
                        this._iState++;
                    }
                },

                _intervalOnSelect : function(t,a,o) {
                    // Get selected dates
                    var dates = this.getSelectedDates();
                    if(dates.length > 1) {
                        /* If more than one date is selected, ensure that the entire interval
                            between and including them is selected */
                        var l = dates[0];
                        var u = dates[dates.length - 1];
                        this.cfg.setProperty("selected", this._dateIntervalString(l, u), false);
                    }
                    // Render changes
                    this.render();
                },

                _intervalOnBeforeDeselect : function(t,a,o) {
                    if(this._iState != 0) {
                        /* If part of an interval is already selected, then swallow up
                            this event because it is superfluous (see _intervalOnDeselect) */
                        return false;
                    }
                },

                _intervalOnDeselect : function(t,a,o) {
                    if(this._iState != 0) {
                        // If part of an interval is already selected, then first deselect all
                        this._iState = 0;
                        this.deselectAll();

                        // Get individual date deselected and page containing it
                        var d = a[0];
                        var date = YAHOO.widget.DateMath.getDate(d[0], d[1] - 1, d[2]);
                        var page = this.getCalendarPage(date);
                        if(page) {
                            // Now (re)select the individual date
                            page.beforeSelectEvent.fire();
                            this.cfg.setProperty("selected", this._dateString(date), false);
                            page.selectEvent.fire([d]);
                        }
                        // Swallow up since we called deselectAll above
                        return false;
                    }
                }
            });

            YAHOO.namespace("example.calendar");
            YAHOO.example.calendar.IntervalCalendar = IntervalCalendar;
        })();';

        $locale = LocaleHandler::getInstance();

        $res .= '
        YAHOO.util.Event.onDOMReady(function()
        {
            var inTxt  = YAHOO.util.Dom.get("'.$this->name_from.'");
            var outTxt = YAHOO.util.Dom.get("'.$this->name_to.'");

            var inDate, outDate, interval;

            inTxt.value  = "";
            outTxt.value = "";

            var cal = new YAHOO.example.calendar.IntervalCalendar("'.$this->div_name.'", {pages:2});

            cal.cfg.setProperty("start_weekday", '.  $this->start_weekday.');
            cal.cfg.setProperty("MONTHS_SHORT", '.   jsArray1D($locale->handle->month_short, false).');
            cal.cfg.setProperty("MONTHS_LONG", '.    jsArray1D($locale->handle->month_long, false).');
            cal.cfg.setProperty("WEEKDAYS_1CHAR", '. jsArray1D($locale->handle->weekday_1char, false).');
            cal.cfg.setProperty("WEEKDAYS_SHORT", '. jsArray1D($locale->handle->weekday_short, false).');
            cal.cfg.setProperty("WEEKDAYS_MEDIUM", '.jsArray1D($locale->handle->weekday_medium, false).');
            cal.cfg.setProperty("WEEKDAYS_LONG", '.  jsArray1D($locale->handle->weekday_long, false).');

            cal.selectEvent.subscribe(function() {
                interval = this.getInterval();

                if (interval.length == 2) {
                    inDate = interval[0];
                    inTxt.value =  inDate.getFullYear() + "-" + (inDate.getMonth() + 1) + "-" + inDate.getDate();

                    if (interval[0].getTime() != interval[1].getTime()) {
                        outDate = interval[1];
                        outTxt.value = outDate.getFullYear() + "-" + (outDate.getMonth() + 1) + "-" + outDate.getDate();
                    } else {
                        outTxt.value = "";
                    }
                }
            }, cal, true);

            cal.render();
        });';

        return '<script type="text/javascript">'.$res.'</script>';
    }

}

?>
