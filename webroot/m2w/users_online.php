<?
	require_once('config.php');

	require_once('vxml_head.php');
?>

	<!-- show current status -->
	<menu id="mnuOnlineStats">
		<pse_audio src="media://m2w/jingle" repeat="LOOP"/>
	  <pse_video src="media://cnt/cntOnline<?=getActiveCalls();?>" repeat="LOOP"/>
		<choice dtmf="0" expr="URLvideochat"></choice>				<!-- go back to chat menu -->
	</menu>

<?
	require_once('vxml_foot.php');
?>
