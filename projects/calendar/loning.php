<?php

require_once('functions_icalendar.php');

header('Content-Type: text/plain; charset="UTF-8"');

iCalBegin('VCALENDAR', 'Månadslön');
for ($i = date('Y')-1; $i <= date('Y')+1; $i++)
{
	$cal = calPaydaysMonthly($i, 25);
	iCalEvents($cal, 'Europe/Stockholm');	//$cal->renderiCal();
}
iCalEnd('VCALENDAR');

?>
