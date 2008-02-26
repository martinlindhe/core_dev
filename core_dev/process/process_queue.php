<?
	//this script is intended to be called regularry. every 30-60 seconds or so
	set_time_limit(60*10);	//10 minute max, for long video recodings
	$config['no_session'] = true;	//force session "last active" update to be skipped
	require_once('config.php');

	do {
		processQueue();
		sleep(1);
	} while (1);

	//include('design_head.php'); $db->showProfile();

?>