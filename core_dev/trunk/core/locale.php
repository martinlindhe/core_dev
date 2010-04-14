<?php
/**
 * $Id$
 *
 * Locale functions for multi-language support
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
}

class LocaleHandler
{
    static $_instance; ///< singleton class
    var $handle;
    private $locale;  ///< 6-letter string representing current locale (ISO 639-2)

    private function __construct()
    {
    }

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
        $this->locale = $s;

        switch ($s) {
        case 'swe': $this->handle = new Locale_SWE(); break;
        case 'eng': $this->handle = new Locale_ENG(); break;
        case 'ger': $this->handle = new Locale_GER(); break;
        default: throw new Exception('Unknown locale '.$s);
        }
    }

    function get() { return $this->locale; }

    /**
     * @param $n month number (1-12)
     */
    function getMonthLong($n) { return $this->handle->month_long[$n-1]; }

    /**
     * @param $n weekday number (0-6), 0=sunday, 1=monday
     */
    function getWeekdayLong($n) { return $this->handle->weekday_long[$n]; }

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
