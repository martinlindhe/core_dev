<?php

require_once('output_ical.php');

$cal = new ical('Månadslön');

for ($i = date('Y')-1; $i <= date('Y')+1; $i++)
{
	$e = $cal->paydaysMonthly($i, 25, 'Löning');
	$cal->addDateEvents($e);
}

$cal->output();

?>
