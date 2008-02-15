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
		global $config;
		if (empty($config['language'])) return $s;

		switch ($config['language']) {
			case 'se': return t_se($s);	//Swedish
			default: die('Unhandled language: '.$config['language']);
		}
	}

	/**
	 * Translates strings into Swedish
	 */
	function t_se($s)
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
			case 'Delete': return 'Radera';
			case 'From': return 'Från';
			case 'To': return 'Till';
			case 'Next': return 'Nästa';
			case 'Previous': return 'Föregående';
			case 'Reply': return 'Svara';

			//Search
			case 'Show usernames beginning with': return 'Visa användarnamn som börjar med';
			case 'Usernames beginning with': return 'Användarnamn som börjar med';
			case 'Free-text': return 'Fritext';
			case 'This field can only be used in searches by admins': return 'Detta fält kan endast sökas på av admins';
			case 'Search result for': return 'Sökresultat på';
			case 'Custom search result': return 'Anpassat sökresultat';
			case ' hits': return ' träffar';
			case '1 hit': return 'en träff';
			case 'New search': return 'Ny sökning';
			case 'Has image': return 'Har bild';
			case 'Select region': return 'Välj län';
			case 'Select city': return 'Välj ort';

			//Abuse report of user
			case 'Report': return 'Anmäl';
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
			case 'Unread': return 'Oläst';

			//Guestbook Conversation
			case 'Guestbook conversation': return 'Gästbokskonversation';

			//Pager
			case 'Page': return 'Sida';
			case 'of': return 'av';
			case 'displaying': return 'visar';
			case 'items': return 'objekt';

			//Messages
			case 'at': return 'vid';
			case 'No subject': return 'Ingen rubrik';
			case 'System message': return 'Systemmeddelande';
			case 'No messages': return 'Inga meddelanden';
			case 'INBOX': return 'Inkorgen';
			case 'OUTBOX': return 'Utkorgen';
			case 'UNREAD': return 'Oläst';
			case 'READ': return 'Läst';
			case 'Return to message overview': return 'Återgå till meddelande-översikten';

			//Polls
			case 'Active poll': return 'Aktuell omröstning';
			case 'Starts': return 'Börjar';
			case 'ends': return 'slutar';
			case 'Save as .csv': return 'Spara som .csv';
			case 'Your vote has been registered.': return 'Din röst har registrerats';
			case 'You already voted, showing current standings': return 'Du har redan röstat, visar nuvarande ställningar';
			case 'The poll closed, final result': return 'Omröstningen har avslutats, slutgiltigt resultat';
			case 'got': return 'fick';
			case 'votes': return 'röster';
			case 'No polls are currently active': return 'Inga omröstningar är aktiva för tillfället';

			//Settings
			case 'No email entered!': return 'Du angav ingen e-postaddress!';
			case 'The email entered is not valid!': return 'E-postaddressen du angav är ogiltig!';
			case 'The email entered already taken!': return 'E-postaddressen du angav är upptagen!';
			case 'The Swedish SSN you entered is not valid!': return 'Personnumret du angav är ogiltigt!';
			case 'The Swedish zipcode you entered is not valid!': return 'Postnummret du angav är ogiltigt!';
			case 'Delete image': return 'Radera bilden';
			case 'Year': return 'År';
			case 'Month': return 'Månad';
			case 'Day': return 'Dag';

			// Contacts
			case ' has blocked you.': return ' har blockerat dig.';
			case 'User blocking': return 'Användarblockering';

			//Registration
			case 'Username contains invalid spaces': return 'Användarnamnet innehåller ogiltiga mellanslag';
			case 'Username or password contains invalid characters': return 'Användarnamnet eller lösenordet innehåller ogiltiga tecken';
			case 'Username must be at least 3 characters long': return 'Användarnamnet måste vara åtminstonde 3 tecken långt';
			case 'Password must be at least 4 characters long': return 'Lösenordet måste vara åtminstonde 4 tecken långt';
			case 'The passwords doesnt match': return 'Lösenorden matchar inte';
			case 'Username is not allowed': return 'Användarnamnet är inte tillåtet';
			case 'Username already exists': return 'Användarnamnet är upptaget';
			case 'Your account has been activated.': return 'Ditt konto har aktiverats.';
			case 'You can now proceed to': return 'Du kan nu fortsätta att';
			case 'log in': return 'logga in';
			case 'Activation code is invalid or expired.': return 'Aktiveringskoden är ogiltig eller föråldrad.';
			case 'Registration failed': return 'Registreringen misslyckades';

			//Session errors
			case 'The page you requested requires you to be logged in.': return 'Sidan du försöker visa kräver att du är inloggad.';
			case 'The page you requested requires admin rights to view.': return 'Sidan du försöker visa kräver admin rättigheter.';
			case 'The page you requested requires superadmin rights to view.': return 'Sidan du försöker visa kräver superadmin rättigheter.';
			case 'No errors to display.': return 'Det finns inga felmeddelanden att visa.';
			case 'Client IP changed.': return 'Klientens IP-nummer har ändrats.';
			case 'Inactivity timeout.': return 'Du har blivit utloggad på grund av inaktivitet.';
			case 'Client user agent string changed.': return 'Klientens user agent har ändrats.';

			// Login
			case 'Login failed': return 'Inloggning misslyckades';
			case 'This account has not yet been activated.': return 'Kontot har inte aktiverats ännu.';
			case 'Logins currently not allowed.': return 'Inloggningar är för närvarande inte tillåtna.';
			case 'The specified email address does not match any registered user.': return 'Ingen användare har en sådan epost-adress.';
			case 'Forgot password': return 'Glömt lösenord';
			case 'Account activation': return 'Aktivera konto';

			//Comments
			case 'comment': return 'kommentar';
			case 'comments': return 'kommentarer';
			case 'Add comment': return 'Spara';

			//News
			case 'Add news': return 'Skapa nyhet';
			case 'Manage news': return 'Redigera nyheter';

			//Textformat
			case 'wrote': return 'skrev';
			case 'Quote': return 'Citat';

			//Files
			case 'Files': return 'Filer';
			case 'New file category': return 'Ny filkategori';
			case 'Upload': return 'Ladda upp';
			case 'Close': return 'Stäng';
			case 'Download': return 'Spara';
			case 'Pass thru': return 'Visa endast';
			case 'Cut': return 'Beskär';
			case 'Resize': return 'Ändra storlek';
			case 'Rotate left': return 'Rotera åt vänster';
			case 'Rotate right': return 'Rotera åt höger';
			case 'Move image': return 'Flytta bild';
			case 'View log': return 'Besökslogg';
			case 'Comments': return 'Kommentarer';

			//Categories
			case 'Global categories': return 'Globala kategorier';
			case 'Your categories': return 'Dina kategorier';
			case 'Private': return 'Privat';
			case 'Hidden': return 'Dold';

			default: return '__('.$s.')__';
		}
	}
?>
