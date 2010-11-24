<?php
/**
 * $Id$
 *
 * Renders a Yahoo UI date selector popup (javascript)
 *
 * Documentation:
 * http://developer.yahoo.com/yui/calendar/
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: hide useless "Reset" and "close" buttons in the bottom

require_once('output_js.php');

class YuiDatePopup
{
    private $name          = 'yui_datepop';  // name of input field which stores selected date
    private $start_weekday = 1; //0=sundays, 1=mondays
    private $selected_date;
    private $button_name;

    function __construct()
    {
        $this->button_name = 'yui_dp_show_'.mt_rand(0,99999);
    }

    function setName($s) { $this->name = $s; }
    function setStartWeekday($n) { $this->start_weekday = $n; }

    function setSelection($date)
    {
        $this->selected_date = ts($date);
    }

    function render()
    {
        $header = XhtmlHeader::getInstance();

        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/fonts/fonts-min.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/button/assets/skins/sam/button.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/container/assets/skins/sam/container.css');
        $header->includeCss('http://yui.yahooapis.com/2.8.2r1/build/calendar/assets/skins/sam/calendar.css');

        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/dragdrop/dragdrop-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/element/element-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/button/button-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/container/container-min.js');
        $header->includeJs('http://yui.yahooapis.com/2.8.2r1/build/calendar/calendar-min.js');

        $header->embedCss(
        // Clear calendars float, using dialog inbuilt form element
        '#container .bd form {'.
            'clear:left;'.
        '}'.

        // Have calendar squeeze upto bd bounding box
        '#container .bd {'.
            'padding:0;'.
        '}'.

        '#container .hd {'.
            'text-align:left;'.
        '}'.

        // Center buttons in the footer
        '#container .ft .button-group {'.
            'text-align:center;'.
        '}'.

        // Prevent border-collapse:collapse from bleeding through in IE6, IE7
        '#container_c.yui-overlay-hidden table {'.
            '*display:none;'.
        '}'.

        // Remove calendars border and set padding in ems instead of px, so we can specify an width in ems for the container
        '#cal {'.
            'border:none;'.
            'padding:1em;'.
        '}'
        );

        $locale = LocaleHandler::getInstance();

        $res =
        'YAHOO.util.Event.onDOMReady(function(){'.

            'var Event = YAHOO.util.Event,'.
                'Dom = YAHOO.util.Dom,'.
                'dialog,'.
                'calendar;'.

            'var showBtn = Dom.get("'.$this->button_name.'");'.

            'Event.on(showBtn, "click", function() {'.

                // Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners, until the first time the button is clicked.
                'if (!dialog) {'.

                    // Hide Calendar if we click anywhere in the document other than the calendar
                    'Event.on(document, "click", function(e) {'.
                        'var el = Event.getTarget(e);'.
                        'var dialogEl = dialog.element;'.
                        'if (el != dialogEl && !Dom.isAncestor(dialogEl, el) && el != showBtn && !Dom.isAncestor(showBtn, el)) {'.
                            'dialog.hide();'.
                        '}'.
                    '});'.

                    'function resetHandler() {'.
                        // Reset the current calendar page to the select date, or to today if nothing is selected.
                        'var selDates = calendar.getSelectedDates();'.
                        'var resetDate;'.

                        'if (selDates.length > 0) {'.
                            'resetDate = selDates[0];'.
                        '} else {'.
                            'resetDate = calendar.today;'.
                        '}'.

                        'calendar.cfg.setProperty("pagedate", resetDate);'.
                        'calendar.render();'.
                    '}'.

                    'function closeHandler() {'.
                        'dialog.hide();'.
                    '}'.

                    'dialog = new YAHOO.widget.Dialog("container", {'.
                        'visible:false,'.
                        'context:["'.$this->button_name.'", "tl", "bl"],'.
                        'buttons:[ {text:"Reset", handler: resetHandler, isDefault:true}, {text:"Close", handler: closeHandler}],'.
                        'draggable:false,'.
                        'close:true'.
                    '});'.
                    'dialog.setHeader("Pick A Date");'.
                    'dialog.setBody(\'<div id="cal"></div>\');'.
                    'dialog.render(document.body);'.

                    'dialog.showEvent.subscribe(function() {'.
                        'if (YAHOO.env.ua.ie) {'.
                            // Since were hiding the table using yui-overlay-hidden, we
                            // want to let the dialog know that the content size has changed, when shown
                            'dialog.fireEvent("changeContent");'.
                        '}'.
                    '});'.
                '}'.

                // Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
                'if (!calendar) {'.

                    'calendar = new YAHOO.widget.Calendar("cal", {'.
                        'iframe:false,'.          // Turn iframe off, since container has iframe support.
                        'hide_blank_weeks:true,'.  // Enable, to demonstrate how we handle changing height, using changeContent

                        ($this->selected_date ?
                            'selected:"'.js_date($this->selected_date).'",'.
                            'pagedate:"'.date('n/Y', $this->selected_date).'",'
                            : ''
                        ).
                        'start_weekday:'.  $this->start_weekday.','.
                        'MONTHS_SHORT:['.   jsArrayFlat($locale->handle->month_short, false).'],'.
                        'MONTHS_LONG:['.    jsArrayFlat($locale->handle->month_long, false).'],'.
                        'WEEKDAYS_1CHAR:['. jsArrayFlat($locale->handle->weekday_1char, false).'],'.
                        'WEEKDAYS_SHORT:['. jsArrayFlat($locale->handle->weekday_short, false).'],'.
                        'WEEKDAYS_MEDIUM:['.jsArrayFlat($locale->handle->weekday_medium, false).'],'.
                        'WEEKDAYS_LONG:['.  jsArrayFlat($locale->handle->weekday_long, false).'],'.
                    '});'.
                    'calendar.render();'.

                    'calendar.selectEvent.subscribe(function() {'.
                        'if (calendar.getSelectedDates().length > 0) {'.
                            'var selDate = calendar.getSelectedDates()[0];'.
                            'Dom.get("'.$this->name.'").value = selDate.getFullYear() + "-" + (selDate.getMonth() + 1) + "-" + selDate.getDate();'.
                        '} else {'.
                            'Dom.get("'.$this->name.'").value = "";'.
                        '}'.
                        'dialog.hide();'.
                    '});'.

                    'calendar.renderEvent.subscribe(function() {'.
                        // Tell Dialog its contents have changed, which allows
                        // container to redraw the underlay (for IE6/Safari2)
                        'dialog.fireEvent("changeContent");'.
                    '});'.
                '}'.

                'var seldate = calendar.getSelectedDates();'.

                'if (seldate.length > 0) {'.
                    // Set the pagedate to show the selected date if it exists
                    'calendar.cfg.setProperty("pagedate", seldate[0]);'.
                    'calendar.render();'.
                '}'.

                'dialog.show();'.
            '});'.
        '});';

        return
        '<button type="button" id="'.$this->button_name.'" title="Show Calendar">'.
            '<img src="'.relurl('core_dev/gfx/icon_calendar.png').'" alt="Calendar"/>'.
        '</button>'.
        js_embed($res);
    }

}

?>
