<?
	/*
		AJAX funktion
		
		Returnerar lite data för angiven mätpunkt & tidsperiod
		
		GET-parametrar:
			id - ID för mätpunkten

			d = day mode, value = timestamp
			w = week mode, value = timestamp
			m = month mode, value = timestamp
			f = free mode, value = timespan, format "2006-1-1_2006-10-10" (YYYY-M-D)
	*/

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$trackId = $_GET['id'];

	//Will return with $time_from, $time_to set
	include('timeperiod_selector.php');
	if (!$time_from || !$time_to) die;
	if ($time_to <= $time_from) die;
	if ($time_from >= time()) die;
	if ($time_to > time()) $time_to = time();

	include('include_all.php');

	//Only respond to AJAX queries from logged in users
	if (empty($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) die;

	$total = getTrackerEntriesTimeperiodCnt($db, $trackId, $time_from, $time_to);
	$unique = getUniqueIPCountFromTrackerEntriesTimeperiod($db, $trackId, $time_from, $time_to, false);

	header('Content-Type: text/xml');

	echo '<?xml version="1.0"?>';

	echo '<d>';
		echo '<t>'.$total.'</t>';		//Totalt antal mätpunkter registrerade
		echo '<u>'.$unique.'</u>';	//Antal unika besökare under tidsperioden
	echo '</d>';

?>