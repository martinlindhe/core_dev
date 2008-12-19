<?php

require_once('output_ical.php');

$cal = new ical('Svenska Helgdagar');

for ($i = date('Y')-1; $i <= date('Y')+1; $i++)
{
	$e = $cal->daysOffSwe($i);
	$cal->addEvents($e, 'Europe/Stockholm');
}

$cal->output();

?>
