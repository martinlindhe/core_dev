<?
/**
 * $Id$
 *
 * Locale functions. core-dev strings are in English internally, this module translates to swedish currently
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	/**
	 * Translates strings into other languages
	 */
	function t($s)
	{
		switch ($s) {
			case 'Forgot password': return 'Glömt lösenord';
			case 'Account activation': return 'Aktivera konto';
			default: return '__('.$s.')__';
		}
	}
?>