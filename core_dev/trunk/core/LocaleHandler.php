<?php
/**
 * $Id$
 *
 * Locale handling for multi-language support
 *
 * Uses ISO 639-2 language codes, see http://en.wikipedia.org/wiki/List_of_ISO_639-2_codes
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: WIP

require_once('locale_swe.php');
require_once('locale_eng.php');
require_once('locale_ger.php');

abstract class CoreLocale
{
    abstract function formatCurrency($n);
}

class LocaleHandler
{
    static $_instance; ///< singleton
    var $handle;
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
        case 'swe': $this->handle = new Locale_SWE(); break;
        case 'eng': $this->handle = new Locale_ENG(); break;
        case 'ger': $this->handle = new Locale_GER(); break;
        default: throw new Exception('Unknown locale '.$s);
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

    /** @param $s 3-letter country code (SWE, NOR) */
    function getCountryName($s)
    {
        $s = strtoupper($s);
        if (!isset($this->handle->country_3char[$s]))
            throw new Exception ('Unknown country name '.$s);

        return $this->handle->country_3char[$s];
    }


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



/**
 * Translates strings into other languages
 */
function t($s)
{
    $locale = LocaleHandler::getInstance();

    switch ($locale->get()) {
    case 'ger': return $s; //German (Deutsch)   - XXX not translated
    case 'eng': return $s;      //English (System default)
    case 'swe': $t = t_swe($s); break; //Swedish (Svenska)
    default: die('Unhandled language: '.$locale->get());
    }

    if (!$t) {
        dp('Untranslated string: '.$s);
        return '__('.$s.')__';
    }

    return $t;

}

?>
