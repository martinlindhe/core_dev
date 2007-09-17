<?
	require_once('config.php');

	require_once('vxml_head.php');

/*
	2part.php - prata 2part-chat
*/
?>
	<script>
		var localCall = "call://" + connection.psems.callID;
	</script>

  <!-- main menu -->
	<menu id="mnuMain">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuMain1to1" repeat="LOOP"/>

		<choice dtmf="1" next="#mnuChatRoom"></choice>	<!-- see if anyone is available for chat -->
		<choice dtmf="0" next="#quit"></choice>					<!-- hangup -->
	</menu>

	<!-- tell the user if anyone else is on the line -->
	<menu id="mnuChatRoom">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuChatRoom" repeat="LOOP"/>

		<choice dtmf="1" next="#mnuPersistentFeed"></choice>	<!-- test call routing. show video from persistent call -->
		<choice dtmf="3" next="#mirror"></choice>							<!-- test call routing. mirror myself -->
		<choice dtmf="0" next="#mnuMain"></choice>						<!-- go to main menu -->

	</menu>

	<!-- show persistent video feed -->
	<menu id="mnuPersistentFeed">
		<pse_audio src="call://callMartin"/>
		<pse_video src="call://callMartin"/>
		<choice dtmf="0" next="#mnuChatRoom"></choice>	<!-- stop -->
	</menu>

	<!-- see myself -->
	<menu id="mirror">
		<pse_audio expr="localCall"/>
		<pse_video expr="localCall"/>
		<choice dtmf="0" next="#mnuChatRoom"></choice>	<!-- stop -->
	</menu>

  <!-- Quit block -->
	<form id="quit">
		<block>
		  <exit/>
		</block>
	</form>
<?
	require_once('vxml_foot.php');
?>
