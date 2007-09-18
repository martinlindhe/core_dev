<?
	require_once('config.php');

	require_once('vxml_head.php');
?>

	<menu id="mnuDebug">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuDebug" repeat="LOOP"/>

		<choice dtmf="1" next="#mirror"></choice>						<!-- test call routing. mirror myself -->
		<choice dtmf="2" next="#persistentFeed"></choice>		<!-- test call routing. show video from persistent call -->
		<choice dtmf="0" expr="URLmain"></choice>						<!-- go to main menu -->
	</menu>

	<!-- show persistent video feed -->
	<menu id="persistentFeed">
		<pse_audio expr="callPersistent"/>
		<pse_video expr="callPersistent"/>
		<choice dtmf="0" next="#mnuDebug"></choice>	<!-- stop -->
	</menu>

	<!-- see myself -->
	<menu id="mirror">
		<pse_audio expr="callLocal"/>
		<pse_video expr="callLocal"/>
		<choice dtmf="0" next="#mnuDebug"></choice>	<!-- stop -->
	</menu>

<?
	require_once('vxml_foot.php');
?>
