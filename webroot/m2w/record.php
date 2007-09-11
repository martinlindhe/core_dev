<?
	echo file_get_contents('vxml_head.xml');
?>

	<form id="jingle">
		<block>
			<!-- this audio will loop over all the following menus -->
		  <pse_audio src="jingle.wav" repeat="LOOP"/>
		  <goto next="#mnuMain"/>
		 </block>
	</form>

  <!-- main menu -->
	<menu id="mnuMain">
	  <pse_video src="media://m2w/mnuMain1" repeat="LOOP"/>
		
		<choice dtmf="1" next="#mnuRecord"></choice>
		<choice dtmf="2" next="#mnuPlayback"></choice>
		<choice dtmf="0" next="#quit"></choice>
	</menu>

	<menu id="mnuRecord">
	  <pse_video src="media://m2w/mnuRecord1" repeat="LOOP"/>
		
		<choice dtmf="0" next="#mnuMain"></choice>
	</menu>

	<menu id="mnuPlayback">
	  <pse_video src="media://m2w/mnuPlayback1" repeat="LOOP"/>
		
		<choice dtmf="0" next="#mnuMain"></choice>
	</menu>

  <!-- Quit block -->
	<form id="quit">
		<block>
		  <exit/>
		</block>
	</form>
<?
	echo file_get_contents('vxml_foot.xml');
?>
