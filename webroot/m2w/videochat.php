<?
	require_once('config.php');

	require_once('vxml_head.php');

/*
	flow:
	
	1. har man lämnat presentation? om nej - lämna pres
	
	sen meny:
	1. välj "se tillgängliga presentationer"
	2. se hur många som är online på linjen
	3. martin live feed
	4. mirror video

*/
?>

	<!-- tell the user if anyone else is on the line -->
	<menu id="mnuChatRoom">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://m2w/mnuChatRoom" repeat="LOOP"/>

		<choice dtmf="1" next="#mnuPresentations"></choice>		<!-- see available presentations: TODO -->
		<choice dtmf="2" expr="URLusersOnline"></choice>			<!-- see how many are online -->
		<choice dtmf="3" next="#mnuPersistentFeed"></choice>	<!-- test call routing. show video from persistent call -->
		<choice dtmf="4" next="#mirror"></choice>							<!-- test call routing. mirror myself -->
		<choice dtmf="0" expr="URLmain"></choice>							<!-- go to main menu -->
	</menu>

	<!-- show persistent video feed -->
	<menu id="mnuPersistentFeed">
		<pse_audio src="call://callMartin"/>
		<pse_video src="call://callMartin"/>
		<choice dtmf="0" next="#mnuChatRoom"></choice>	<!-- stop -->
	</menu>

	<!-- see myself -->
	<menu id="mirror">
		<pse_audio expr="callLocal"/>
		<pse_video expr="callLocal"/>
		<choice dtmf="0" next="#mnuChatRoom"></choice>	<!-- stop -->
	</menu>

<?
	require_once('vxml_foot.php');
?>
