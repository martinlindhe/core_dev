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
			case 'Remove': return 'Radera';
			case 'From': return 'Från';
			case 'Next': return 'Nästa';
			case 'Previous': return 'Föregående';

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

			//Abuse report of user
			case 'Report user': return 'Anmäl användaren';
			case 'Please choose the reason as to why you wish to report this user': return 'Ange varför du vill anmäla denna användare';
			case 'Reason': return 'Anledning';
			case 'Please describe your reason for the abuse report': return 'Var god komplettera anmälan med ytterligare information';
			case 'Send report': return 'Skicka anmälan';
			case 'Harassment': return 'Trakasseri';
			case 'Other': return 'Annat';
			case 'Thank you. Your report has been recieved.': return 'Tack för din anmälan.';

			//Guestbook
			case 'Guestbook': return 'Gästbok';
			case 'contains': return 'innehåller';
			case 'messages': return 'meddelanden';
			case 'New entry': return 'Nytt inlägg';

			//Pager
			case 'Page': return 'Sida';
			case 'of': return 'av';
			case 'displaying': return 'visar';
			case 'items': return 'objekt';

			case 'Delete image': return 'Radera bilden';

			case 'Forgot password': return 'Glömt lösenord';
			case 'Account activation': return 'Aktivera konto';
			default: return '__('.$s.')__';
		}
	}
?>