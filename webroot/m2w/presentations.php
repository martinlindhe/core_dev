<?
	require_once('config.php');

	require_once('vxml_head.php');
?>

	<menu id="mnuPresentations">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuPresentations" repeat="LOOP"/>

		<choice dtmf="1" next="#xxx"></choice>							<!-- 1) send message to this user -->
		<choice dtmf="2" next="#persistentFeed"></choice>		<!-- 2) see next presentation -->
		<choice dtmf="2" next="#xxx"></choice>							<!-- 3) report abuse! TODO -->
		<choice dtmf="0" expr="URLmain"></choice>						<!-- go to main menu -->
	</menu>

<?
	require_once('vxml_foot.php');
?>
