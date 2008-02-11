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
			case 'Save': return 'Spara';
			case 'Lock': return 'Lås';
			case 'Unlock': return 'Lås upp';
			case 'Edit': return 'Redigera';
			case 'History': return 'Historik';
			case 'Files': return 'Filer';
			case 'Search': return 'Sök';

			//class.Users.php search
			case 'Show usernames beginning with': return 'Visa användarnamn som börjar med';
			case 'Usernames beginning with': return 'Användarnamn som börjar med';
			case 'Free-text': return 'Fritext';
			case 'This field can only be used in searches by admins': return 'Detta fält kan endast sökas på av admins';
			case 'Search result for': return 'Sökresultat på';
			case 'Custom search result': return 'Anpassat sökresultat';
			case ' hits': return ' träffar';
			case '1 hit': return 'en träff';
			case 'New search': return 'Ny sökning';

			case 'Delete image': return 'Radera bilden';

			case 'Forgot password': return 'Glömt lösenord';
			case 'Account activation': return 'Aktivera konto';
			default: return '__('.$s.')__';
		}
	}
?>