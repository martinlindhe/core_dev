<?php
/**
 *
 */

//STATUS: wip

class Locale_ENG extends CoreLocale
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
    'SWE' => 'Sweden',
    'NOR' => 'Norway',
    'USA' => 'United States of America',
    'GBR' => 'United Kingdom',
    'DEU' => 'Germany',
    );

    function formatCurrency($n)
    {
        die('formatCurrency ENG TODO');
    }

    function getLanguageCode() { return 'en'; }

}

?>
