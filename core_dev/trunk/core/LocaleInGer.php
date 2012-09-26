<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

class LocaleInGer
{
    var $month_long = array(
        'Januar', 'Februar', 'März',
        'April', 'Mai', 'Juni',
        'Juli', 'August', 'September',
        'Oktober', 'November', 'Dezember');

    var $month_short = array(
        'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun',
        'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez');

    var $weekday_long = array(
        'Sonntag', 'Montag', 'Dienstag', 'Mittwoch',
        'Donnerstag', 'Freitag', 'Samstag');

    var $weekday_medium = array(
        'Son', 'Mon', 'Die', 'Mit', 'Don', 'Fre', 'Sam');

    var $weekday_short = array(
        'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');

    var $weekday_1char = array(
        'S', 'M', 'D', 'M', 'D', 'F', 'S');

    function formatCurrency($n)
    {
        die('formatCurrency GER TODO');
    }

    function getSkycondition($s)
    {
        throw new Exception ('FIXME translate skyconditon to german');
    }

}

?>
