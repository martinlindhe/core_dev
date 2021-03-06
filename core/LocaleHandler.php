<?php
/**
 * $Id$
 *
 * Locale handling for multi-language support
 *
 * References
 * ----------
 * ISO 639-2 (language codes): http://en.wikipedia.org/wiki/List_of_ISO_639-2_codes
 * ISO 3166-1 (country codes): http://en.wikipedia.org/wiki/ISO_3166-1
 *
 * @author Martin Lindhe, 2007-2012 <martin@ubique.se>
 */

//STATUS: WIP

//TODO: fix class nesting crap, LocaleHandler should just be the current locale, no $this->handle crap

namespace cd;

require_once('LocaleInSwe.php');
require_once('LocaleInEng.php');
require_once('LocaleInGer.php');

class LocaleHandler
{
    static  $_instance;    ///< singleton
    var     $handle;
    private $locale = '';  ///< 3-letter string representing current locale (ISO 639-2)

    private function __construct()
    {
        //defaults to english
        $this->set('eng');
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Set current locale
     */
    function set($s)
    {
        $this->locale = strtolower($s);

        switch ($s) {
        case 'eng': $this->handle = new LocaleInEng(); break;
        case 'swe': $this->handle = new LocaleInSwe(); break;
        case 'ger': $this->handle = new LocaleInGer(); break;
        default: throw new \Exception('Unknown locale '.$s);
        }
    }

    function get() { return $this->locale; }

    function formatCurrency($n) { return $this->handle->formatCurrency($n); }

    /** @param $n month number (1-12) */
    function getMonthLong($n) { return $this->handle->month_long[$n-1]; }
    function getMonthsLong() { return $this->handle->month_long; }
    function getMonthShort($n) { return $this->handle->month_short[$n-1]; }
    function getMonthsShort() { return $this->handle->month_short; }

    /** @param $n weekday number (0-6), 0=sunday, 1=monday */
    function getWeekdayLong($n) { return $this->handle->weekday_long[$n]; }
    function getWeekdayMedium($n) { return $this->handle->weekday_medium[$n]; }
    function getWeekdayShort($n) { return $this->handle->weekday_short[$n]; }
    function getWeekday1Char($n) { return $this->handle->weekday_1char[$n]; }

    function getSkycondition($s) { return $this->handle->getSkycondition($s); }

    /**
     * @param $s duration (in english), translates to current locale
     */
    function translateDuration($s)
    {
        if (array_key_exists($s, $this->handle->durations))
            return $this->handle->durations [ $s ];

        return 'XXX-FAILTRANS-'.$s.'XXX';
    }

}

?>
