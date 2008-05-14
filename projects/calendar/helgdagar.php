<?php
//XXX: can you get ical file over webdav (if possible, behind https) into outlook?

require_once('functions_icalendar.php');

header('Content-Type: text/plain; charset="UTF-8"');

iCalBegin('VCALENDAR', 'Svenska Helgdagar');
for ($i = date('Y')-1; $i <= date('Y')+1; $i++)
{
	$cal = calDaysOffSwe($i);
	iCalEvents($cal, 'Europe/Stockholm');	//$cal->renderiCal();
}
iCalEnd('VCALENDAR');

?>
