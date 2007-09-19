<?
	require_once('config.php');

	require_once('vxml_head.php');

/*
	flow:
	
	1. har man lämnat presentation? om nej - lämna pres
	
	sen meny:
	1. välj "se tillgängliga presentationer"
	2. se hur många som är online på linjen
	3. ändra min presentation

*/
?>

	<!-- if we made a presentation recording, jump to mnuChatRoom. else let user make presentation -->
	<form id="checkForPresentation">
		<block>
			<if cond="(has_pres == 0)">
				<goto next="#infoRecordStarting"/>
			<else/>
				<goto next="#mnuChatRoom"/>
			</if>
		</block>
	</form>

	<!-- tell the user if anyone else is on the line -->
	<menu id="mnuChatRoom">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuChatRoom" repeat="LOOP"/>

		<choice dtmf="1" expr="URLpresentations"></choice>		<!-- see available presentations -->
		<choice dtmf="2" expr="URLusersOnline"></choice>			<!-- see how many are online -->
		<choice dtmf="3" next="#mnuShowMyPres"></choice>			<!-- see my current presentation -->
		<choice dtmf="4" next="#frmRecord"></choice>					<!-- record a new presentation -->

		<choice dtmf="0" expr="URLmain"></choice>							<!-- go to main menu -->
	</menu>

	<!-- tells the user "please leave presentation" then jumps to frmRecord -->
	<form id="infoRecordStarting">
		<block>
			<pse_video src="media://m2w/infoRecordStarting" timeout="4000"/>
			<goto next="#frmRecord"/>
		</block>
	</form>

	<!-- record section -->
	<form id="frmRecord">
		<pse_record name="record_temp" maxtime="30s" dtmfterm="false">
			<!-- actual recording starts as soon as this video prompt is displayed -->
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
		<block>
			<pse_audio expr="record_var"/>
			<pse_video expr="record_var"/>
			<goto next="#mnuPreviewRecording"/>
		</block>
	</form>

	<form id="frmStore">
		<block>
			<!-- fixme: filen skapas men status blir ibland "OFFLINE" - "Internal error (unspecified error)", samma med dest="media://m2w/recordedContent"-->
			<pse_submit src="record_var" destexpr="upload_path" notifyUrl="http://192.168.0.210/m2w/pres_stored.php?id=<?=$_SESSION['user_id']?>"/>

			<assign name="has_pres" expr="'1'"/>

			<!-- show "msg has been stored" for 5 sec then go back to main menu -->
			<pse_audio src="media://examples/silence"/>
			<pse_video src="media://m2w/frmStore" timeout="4000"/>

			<goto next="#mnuChatRoom"/>
		</block>
	</form>

	<!-- shows my current presentation video. press 0 to go back -->
	<menu id="mnuShowMyPres">
		<pse_audio expr="upload_path" repeat="LOOP"/>
		<pse_video expr="upload_path" repeat="LOOP"/>

		<choice dtmf="0" next="#mnuChatRoom"></choice>		<!-- go back -->
	</menu>

<?
	require_once('vxml_foot.php');
?>
