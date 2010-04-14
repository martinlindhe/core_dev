<?php
/**
 * $Id$
 *
 * Locale functions for multi-language support
 *
 * Uses ISO 639-2 language codes, see http://en.wikipedia.org/wiki/List_of_ISO_639-2_codes
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('locale_swe.php');
require_once('locale_ger.php');

/**
 * Translates strings into other languages
 */
function t($s)
{
	global $config;
	if (empty($config['language'])) return $s;

	switch ($config['language']) {
	case 'eng': case 'en': return $s;      //English (System default)
	case 'swe': case 'sv': $t = t_swe($s); break; //Swedish (Svenska)
	//case 'ger': case 'de': $t = t_ger($s); break; //German (Deutsch)
	default: die('Unhandled language: '.$config['language']);
	}

	if (!$t) {
		dp('Untranslated string: '.$s);
		return '__('.$s.')__';
	}

	return $t;

}

?>
