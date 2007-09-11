<?
	require_once('config.php');

	require_once('vxml_head.php');

/*
	record.php - implements video/audio recording, preview, store and play-on-demand
*/
?>
	<!-- global behaviour. # will stop on-going recording -->
	<property name="termchar" value="#"/>

	<var name="record_var"/>
	<var name="upload_path" expr="'media://m2w/rec/up_'"/>
	<? setSID(); ?>

  <!-- main menu -->
	<menu id="mnuMain">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuMain" repeat="LOOP"/>
		
		<choice dtmf="1" next="#frmRecord"></choice>		<!-- go to menu to record a video -->
		<choice dtmf="2" next="#mnuPlayback"></choice>	<!-- go to menu to playback stored videos -->
		<choice dtmf="0" next="#quit"></choice>					<!-- hangup -->
	</menu>

	<!-- fixme: implementera -->
	<menu id="mnuPlayback">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuPlayback1" repeat="LOOP"/>	<!-- fixme: bild saknas -->

		<choice dtmf="0" next="#mnuMain"></choice>
	</menu>

	<!-- record section -->
	<form id="frmRecord">
		<pse_record name="record_temp" maxtime="30s" dtmfterm="false">
			<!-- actual recording starts as soon as this audio prompt has finished playing -->
			<pse_audio src="media://examples/record/beep" repeat="LOOP" timeout="4000"/><!-- plays beep for 4 seconds -->
			<pse_video src="media://m2w/frmRecord" repeat="LOOP"/>
		</pse_record>
		<block>
			<!-- the scope of record_temp variable is limited to this <form> tag -->
			<!-- we store it in global variable record_var for later use -->
			<assign name="record_var" expr="record_temp"/>
		</block>

		<block>
			<goto next="#mnuPreviewRecording"/>
		</block>
	</form>


	<menu id="mnuPreviewRecording">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
		<pse_video src="media://m2w/mnuPreviewRecording" repeat="LOOP"/>

		<choice dtmf="1" next="#frmReview"></choice>	<!-- review the newly recorded video -->
		<choice dtmf="2" next="#frmRecord"></choice>	<!-- go back and do a new recoding -->
		<choice dtmf="3" next="#frmStore"></choice>		<!-- store the recording -->
		<choice dtmf="0" next="#mnuMain"></choice>		<!-- go back to main menu -->
	</menu>

	<form id="frmReview">
		<!-- fixme: börjar spelas innnan man får video feedback, hör ljud lite tidigt. går det få in en fördröjning här? -->
		<block>
			<pse_audio expr="record_var"/>
			<pse_video expr="record_var"/>
			<goto next="#mnuPreviewRecording"/>
		</block>
	</form>

	<form id="frmStore">
		<block>
			<!-- fixme: variabel destination, inte dest="media://m2w/recordedContent"-->
			<pse_submit src="record_var" destexpr="upload_path + session_id"/>
		</block>

		<!-- show "msg has been stored" for 5 sec then go back to main menu -->
		<block>
			<pse_audio src="media://examples/silence" repeat="LOOP"/>
			<pse_video src="media://m2w/frmStore" timeout="5000"/>
			<goto next="#mnuMain"/>
		</block>
	</form>

  <!-- Quit block -->
	<form id="quit">
		<block>
		  <exit/>
		</block>
	</form>
<?
	require_once('vxml_foot.php');
?>
