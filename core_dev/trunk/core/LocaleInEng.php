<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

class LocaleInEng extends CoreLocale
{
    var $month_long = array(
        'January', 'February', 'March',
        'April', 'May', 'June',
        'July', 'August', 'September',
        'October', 'November', 'December');

    var $month_short = array(
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

    var $weekday_long = array(
        'Sunday', 'Monday', 'Tuesday', 'Wednesday',
        'Thursday', 'Friday', 'Saturday');

    var $weekday_medium = array(
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

    var $weekday_short = array(
        'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa');

    var $weekday_1char = array(
        'S', 'M', 'T', 'W', 'T', 'F', 'S');

    var $country_3char = array(
    'EUR' => 'European Union',
    'SWE' => 'Sweden',
    'NOR' => 'Norway',
    'USA' => 'United States of America',
    'GBR' => 'United Kingdom',
    'DEU' => 'Germany',
    );

    var $durations = array( //XXX this array can be removed if translateDuration in LocaleHandler can be overridden, need LocaleHandler class nesting fixed first
    'second' =>'second',    'seconds'=>'seconds',
    'minute' =>'minute',    'minutes'=>'minutes',
    'hour'   =>'hour',      'hours'  =>'hours',
    'day'    =>'day',       'days'   =>'days',
    'week'   =>'week',      'weeks'  =>'weeks',
    'month'  =>'month',     'months' =>'months',
    'year'   =>'year',      'years'  =>'years'
    );

    function formatCurrency($n)
    {
        die('formatCurrency ENG TODO');
    }

    function getLanguageCode() { return 'en'; }

    function getSkycondition($s) { return $s; }

}

?>
