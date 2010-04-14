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
		switch ($s) {
		case 'swe': $this->handle = new Locale_SWE(); break;
		case 'eng': $this->handle = new Locale_ENG(); break;
		case 'ger': $this->handle = new Locale_GER(); break;
		default: throw new Exception('Unknown locale '.$s);
		}
	}

}



/**
 * Translates strings into other languages
 */
function t($s)
{
	global $config;
	if (empty($config['language'])) return $s;

	switch ($config['language']) {
	case 'ger': case 'de': return $s; //German (Deutsch)   - XXX not translated
	case 'eng': case 'en': return $s;      //English (System default)
	case 'swe': case 'sv': $t = t_swe($s); break; //Swedish (Svenska)

	default: die('Unhandled language: '.$config['language']);
	}

	if (!$t) {
		dp('Untranslated string: '.$s);
		return '__('.$s.')__';
	}

	return $t;

}

?>
