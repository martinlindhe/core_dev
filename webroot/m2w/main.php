<?
/*
	main.php - main menu (and start menu) for 1-to-1 chat application (M2W)
	
	requires
	vxml_head.php - generic VXML header
	vxml_foot.php - generic VXML footer
	functions_dylogic.php - various functions

	hangup.php - to terminate the call
	videochat.php - for video chat features
*/

	require_once('config.php');

	//Use http://192.168.0.210/m2w/main.php?init from PSE-MS "Call Control"
	if (isset($_GET['init'])) {
		registerCallStart();
	}

	require_once('vxml_head.php');
?>

  <!-- main menu -->
	<menu id="mnuMain">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuMain1to1" repeat="LOOP"/>

		<choice dtmf="1" expr="URLvideochat"></choice>	<!-- see if anyone is available for chat -->
		<choice dtmf="0" expr="URLhangup"></choice>			<!-- hangup -->
	</menu>

<?
	require_once('vxml_foot.php');
?>
