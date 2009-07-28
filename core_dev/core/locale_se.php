<?php
/**
 * $Id$
 *
 * Swedish translation
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

$month_swe = array(
'', 'Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni',
'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December');

$weekday_swe = array(
'', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag', 'Söndag');

$day_suff_swe = array(//den 1:a, 5:e osv...
'', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
'e', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
'e', 'a');

$timeunits_swe = array(
'second' =>'sekund',
'seconds'=>'sekunder',
'minute' =>'minut',
'minutes'=>'minuter',
'hour'   =>'timme',
'hours'  =>'timmar',
'day'    =>'dag',
'days'   =>'dagar',
'week'   =>'vecka',
'weeks'  =>'veckor',
'month'  =>'månad',
'months' =>'månader',
'year'   =>'år',
'years'  =>'år');

$skyconditions_swe = array(
'clear'         => 'molnfritt',
'mostly clear'  => 'mestadels molnfritt',
'mostly cloudy' => 'mestadels molnigt',
//'partly clear'  => 'delvis molnfritt',
'partly cloudy' => 'delvis molnigt'
);

/**
 * Translates strings into Swedish
 */
function t_se($s)
{
	global $timeunits_swe, $skyconditions_swe;

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
		case 'Add': return 'Lägg till';
		case 'Move': return 'Flytta';
		case 'Cancel': return 'Avbryt';
		case 'Modify': return 'Ändra';
		case 'Create': return 'Skapa';
		case 'Update': return 'Uppdatera';
		case 'Test': return 'Testa';
		case 'Activate': return 'Aktivera';
		case 'Show': return 'Visa';
		case 'Change': return 'Ändra';
		case 'Continue': return 'Fortsätt';
		case 'Comment': return 'Kommentar';
		case 'Description': return 'Beskrivning';
		case 'Name': return 'Namn';
		case 'Events': return 'Händelser';
		case 'Userdata': return 'Användardata';
		case 'Forum': return 'Forum';
		case 'Share': return 'Dela';
		case 'Owner': return 'Ägare';
		case 'Time': return 'Tidpunkt';
		case 'Referer': return 'Referens';
		case 'Display': return 'Visa';
		case 'Anonymous': return 'Anonym';

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
		case 'Age': return 'Ålder';
		case 'Select age': return 'Välj ålder';
		case 'to': return 'till';
		case 'Below 18': return 'Under 18';
		case 'Above 65': return 'Över 65';

		//Abuse report of user / images
		case 'Report': return 'Anmäl';
		case 'Report user': return 'Anmäl användaren';
		case 'Please choose the reason as to why you wish to report this user': return 'Ange varför du vill anmäla denna användare';
		case 'Reason': return 'Anledning';
		case 'Please describe your reason for the abuse report': return 'Var god komplettera anmälan med ytterligare information';
		case 'Send report': return 'Skicka anmälan';
		case 'Harassment': return 'Trakasseri';
		case 'Other': return 'Annat';
		case 'Thank you. Your report has been recieved.': return 'Tack för din anmälan.';
		case 'Report file': return 'Anmäl filen';
		case 'Please choose the reason as to why you wish to report this file': return 'Ange varför du vill anmäla denna fil';

		//Guestbook
		case 'Guestbook': return 'Gästbok';
		case 'The guestbook': return 'Gästboken';
		case 'contains': return 'innehåller';
		case 'messages': return 'meddelanden';
		case 'New entry': return 'Nytt inlägg';
		case 'Unread': return 'Oläst';
		case 'Your guestbook is empty': return 'Du har inte fått några inlägg i din gästbok än.';
		case 'The guestbook is empty': return 'Gästboken är tom.';
		case 'Displaying guestbook history between yourself and': return 'Visar gästbokshistoriken mellan dig själv och';
		case 'Return to guestbook overview': return 'Återgå till min gästbok';
		case 'Reply to': return 'Skicka svar till';
		case 'Send reply': return 'Skicka svar';

		//Pager
		case 'Page': return 'Sida';
		case 'of': return 'av';
		case 'displaying': return 'visar';
		case 'items': return 'objekt';
		case 'total': return 'totalt';

		//Messages
		case 'at': return 'vid';
		case 'No subject': return 'Ingen rubrik';
		case 'System message': return 'Systemmeddelande';
		case 'No messages': return 'Inga meddelanden.';
		case 'No messages in inbox': return 'Du har inte fått några mail än.';
		case 'No messages in outbox': return 'Du har inga skickade mail.';
		case 'INBOX': return 'Inkorg';
		case 'OUTBOX': return 'Skickade';
		case 'UNREAD': return 'Oläst';
		case 'READ': return 'Läst';
		case 'Return to message overview': return 'Återgå till meddelande-översikten';
		case 'Send': return 'Skicka';

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

		//Contacts
		case ' has blocked you.': return ' har blockerat dig.';
		case 'User blocking': return 'Användarblockering';

		//Registration
		case 'Username contains invalid spaces': return 'Användarnamnet innehåller ogiltiga mellanslag';
		case 'Username or password contains invalid characters': return 'Användarnamnet eller lösenordet innehåller ogiltiga tecken';
		case 'Username must be at least': return 'Användarnamnet måste vara åtminstonde';
		case 'Password must be at least': return 'Lösenordet måste vara åtminstonde';
		case 'characters long': return 'tecken långt';
		case 'The passwords doesnt match': return 'Lösenorden matchar inte';
		case 'Username is not allowed': return 'Användarnamnet är inte tillåtet';
		case 'Username already exists': return 'Användarnamnet är upptaget';
		case 'Your account has been activated.': return 'Ditt konto har aktiverats.';
		case 'You can now proceed to': return 'Du kan nu fortsätta att';
		case 'log in': return 'logga in';
		case 'Activation code is invalid or expired.': return 'Aktiveringskoden är ogiltig eller föråldrad.';
		case 'Registration failed': return 'Registreringen misslyckades';
		case 'Username': return 'Användarnamn';
		case 'Password': return 'Lösenord';
		case 'Log in': return 'Logga in';
		case 'Register': return 'Registrera konto';
		case 'Again': return 'Upprepa';
		case 'The account you create now will be the super administrator account.'; return 'Kontot du skapar nu kommer att bli super admin kontot.';
		case 'Register new account': return 'Registrera nytt konto';
		case 'User not found': return 'Användaren finns inte';
		case 'An error occured sending activation mail!': return 'Ett fel uppstod när aktiveringsmail skulle skickas ut!';
		case 'An email with your activation code has been sent.': return 'Ett email med din aktiveringskod har skickats.';
		case 'Follow the link in the mail to finish your registration.': return 'Följ länken i mailet för att slutföra din registrering.';
		case 'Ok': return 'Ok';

		//Session errors
		case 'Session timed out.': return 'Sessionen avslutades på grund av inaktivitet.';
		case 'The page you requested requires you to be logged in.': return 'Sidan du försöker visa kräver att du är inloggad.';
		case 'The page you requested requires webmaster rights to view.': return 'Sidan du försöker visa kräver att du är webmaster.';
		case 'The page you requested requires admin rights to view.': return 'Sidan du försöker visa kräver admin rättigheter.';
		case 'The page you requested requires superadmin rights to view.': return 'Sidan du försöker visa kräver superadmin rättigheter.';
		case 'No errors to display.': return 'Det finns inga felmeddelanden att visa.';
		case 'Client IP changed.': return 'Klientens IP-nummer har ändrats.';
		case 'Inactivity timeout.': return 'Du har blivit utloggad på grund av inaktivitet.';
		case 'Client user agent string changed.': return 'Klientens user agent har ändrats.';
		case 'Invalid email address or username': return 'Felaktigt alias eller e-post';
		case 'Problems sending mail': return 'Problem att skicka ut mail';

		// Login
		case 'Login failed': return 'Inloggning misslyckades';
		case 'This account has not yet been activated.': return 'Kontot har inte aktiverats ännu.';
		case 'Logins currently not allowed.': return 'Inloggningar är för närvarande inte tillåtna.';
		case 'The specified email address does not match any registered user.': return 'Ingen användare har en sådan epost-adress.';
		case 'Forgot password': return 'Glömt lösenord';
		case 'Account activation': return 'Aktivera konto';
		case 'Account blocked': return 'Konto blockerat';
		case 'The password is incorrect': return 'Lösenordet är felaktigt';

		//Comments
		case 'comment': return 'kommentar';
		case 'comments': return 'kommentarer';
		case 'Add comment': return 'Spara';

		//News
		case 'Add news': return 'Skapa nyhet';
		case 'Manage news': return 'Redigera nyheter';
		case 'Show news': return 'Visa';
		case 'Polls': return 'Omröstningar';
		case 'Attachments': return 'Bilagor';
		case 'Categories': return 'Kategorier';
		case 'Category': return 'Kategori';
		case 'Edit news article': return 'Redigera nyhetsartikeln';
		case 'Title': return 'Titel';
		case 'Text': return 'Text';
		case 'Time for publication': return 'Tid för publicering';
		case 'Save changes': return 'Spara ändringar';
		case 'Include this news in the RSS feed': return 'Inkludera denna nyhet i RSS-flödet';
		case 'Show article': return 'Visa artikeln';
		case 'Updated': return 'Uppdaterad';
		case 'published': return 'publicerad';
		case 'will be published': return 'kommer att publiceras';

		//Ratings
		case 'Rate this': return 'Ranka detta';
		case 'Rate': return 'Ranka';
		case 'Current rating': return 'Aktuell ställning';
		case 'in': return 'i';
		case 'vote': return 'röst';
		case 'votes': return 'röster';
		case 'Not rated yet.': return 'Inga röster ännu.';

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
		case 'Crop': return 'Beskär';
		case 'Resize': return 'Ändra storlek';
		case 'Rotate left': return 'Rotera åt vänster';
		case 'Rotate right': return 'Rotera åt höger';
		case 'Move image': return 'Flytta bild';
		case 'View log': return 'Besökslogg';
		case 'Comments': return 'Kommentarer';
		case 'Crop selection': return 'Beskär markering';
		case 'Uploaded file is too big': return 'Den uppladdade filen är för stor';
		case 'Updated gallery': return 'Uppdaterat galleri';
		case 'Video awaiting conversion.': return 'Videon väntar på att konverteras';
		case 'Show file category': return 'Visa filkategori';
		case 'Upload a file': return 'Ladda upp en fil';
		case 'Filename': return 'Filnamn';
		case 'Filesize': return 'Filstorlek';
		case 'Uploader': return 'Uppladdad av';
		case 'Uploaded at': return 'Uppladdad';
		case 'Downloaded': return 'Nerladdad';
		case 'Width': return 'Bredd';
		case 'Height': return 'Höjd';
		case 'times': return 'gånger';
		case 'Move the file to': return 'Flytta filen till';
		case 'No description': return 'Beskrivning saknas';

		//Categories
		case 'Global categories': return 'Globala kategorier';
		case 'Your categories': return 'Dina kategorier';
		case 'Private': return 'Privat';
		case 'Hidden': return 'Dold';

		//Wiki
		case 'Unused files': return 'Oanvända filer';
		case 'by': return 'av';
		case 'Add new FAQ': return 'Skapa en ny FAQ';
		case 'Answer': return 'Svar';
		case 'Last edited': return 'Senast redigerad';
		case 'never': return 'aldrig';
		case 'The wiki': return 'Wikin';
		case 'does not yet exist': return 'existerar ännu inte';

		//Change password
		case 'Current password': return 'Nuvarande lösenord';
		case 'New password': return 'Nytt lösenord';
		case 'Repeat password': return 'Upprepa';
		case 'Change password': return 'Ändra lösenordet';
		case 'Current password is incorrect': return 'Nuvarande lösenord är felaktigt';
		case 'Your password has been changed successfully!': return 'Ditt lösenord har ändrats!';
		case 'The passwords doesnt match': return 'Lösenorden matchar inte';
		case 'Password must be at least 4 characters long': return 'Lösenordet måste vara minst 4 tecken långt';

		//Userdata admin
		case 'Edit userdata field': return 'Redigera userdata-fält';
		case 'Make field private': return 'Gör fältet privat';
		case 'Current options': return 'Nuvarande alternativ';
		case 'options': return 'alternativ';
		case 'Field name': return 'Fältnamn';
		case 'Private field': return 'Privat fält';
		case 'Require at registration': return 'Kräv vid registrering';
		case 'May contain HTML': return 'Får innehålla HTML';
		case 'Avatar': return 'Avatar';
		case 'Textarea': return 'Textarea';
		case 'Checkbox': return 'Kryssruta';
		case 'Radio buttons': return 'Radioknappar';
		case 'Dropdown list': return 'Rullgardinslista';
		case 'E-mail': return 'E-post';
		case 'This setting is hidden from other users.': return 'Denna inställning visas inte för andra användare.';
		case 'Create a new userdata field': return 'Skapa ett nytt userdata-fält';
		case 'Type': return 'Typ';
		case 'Cellphone': return 'Mobilnummer';
		case 'Birth date': return 'Födelsedag';
		case 'Birth date (Swedish)': return 'Personnummer (Svenskt)';
		case 'Gender': return 'Kön';
		case 'Are you sure you want to delete this userdata field?': return 'Är du säker på att du vill radera detta userdata-fält?';
		case 'Userdata field successfully deleted!': return 'Userdata-fältet har raderats!';
		case 'Location (Sweden)': return 'Postnummer (Svenskt)';
		case 'Default value': return 'Standard-värde';
		case 'Image': return 'Bild';
		case 'Theme': return 'Tema';
		case 'The uploaded file is not a image. You need to upload a image to use as a presentation image!': return 'Den uppladdade filen är inte en bild. Du måste ladda upp en bild för att använda som presentationsbild!';
		case 'Current profile image': return 'Aktuell profilbild';
		case 'Delete current image': return 'Radera aktuell bild';
		case 'Select a image from your computer': return 'Välj en bild från datorn';
		case 'This image is waiting to be approved': return 'Denna bild väntar på att godkännas';
		case "You currently don't have a profile picture": return 'Du har ingen gammal bild';

		//Forum
		case 'Create new root level category': return 'Skapa ny huvudkategori';
		case 'Last topic': return 'Senaste tråden';
		case 'Topic': return 'Tråd';
		case 'Topics': return 'Trådar';
		case 'Posts': return 'Inlägg';
		case 'Author': return 'Trådskapare';
		case 'Views': return 'Visningar';
		case 'Last post': return 'Senaste inlägget';
		case 'Never': return 'Aldrig';
		case 'Tell a friend': return 'Tipsa en kompis';
		case 'Subject': return 'Ämne';
		case 'Attach a file': return 'Bifoga en fil';
		case 'Write a motivation': return 'Skriv en motivering';

		//Subscriptions
		case 'The user': return 'Användaren';
		case 'has uploaded files to their file area.': return 'har laddat upp nytt innehåll till sitt galleri.';

		//Reset password
		case 'Activation code is invalid or expired.': return 'Aktiveringskoden är ogiltig eller för gammal.';
		case 'Set a new password': return 'Ange ett nytt lösenord';
		case 'Your password has been changed!': return 'Ditt lösenord har ändrats';
		case 'Because we don\'t store the password in clear text it cannot be retrieved.': return 'Eftersom vi inter sparar ditt lösenord i ren text kan det inte återställas.';
		case 'You will therefore need to set a new password for your account.': return 'Därför måste du ange ett nytt lösenord till ditt konto.';
		case 'Set password': return 'Sätt lösenord';

		//Statistics
		case 'Statistics': return 'Statistik';
		case 'Custom report': return 'Anpassad rapport';

		//Moderation / monitoring
		case 'Monitored': return 'Granskad';
	}

	if (!empty($timeunits_swe[strtolower($s)])) return $timeunits_swe[strtolower($s)];
	if (!empty($skyconditions_swe[strtolower($s)])) return $skyconditions_swe[strtolower($s)];

	return '__('.$s.')__';
}
?>
