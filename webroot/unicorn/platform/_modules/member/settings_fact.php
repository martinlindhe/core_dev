<?
	require(CONFIG.'cut.fnc.php');
	require(CONFIG.'secure.fnc.php');

	require_once('settings.fnc.php');

	$head = $user->getcontent($l['id_id'], 'user_head');

	if (!empty($_POST['do'])) {
		storeFacts();

		errorTACT('Uppdaterat!', l('member', 'settings', 'fact'), 1500);
	}
	$page = 'settings_fact';
	//$profile = $user->getcontent($l['id_id'], 'user_profile');

	require(DESIGN.'head.php');
?>
<div id="mainContent">

	<div class="subHead">inställningar</div><br class="clr"/>

	<? makeButton(false, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(true, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'theme').'\')', 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'subscription').'\')', 'icon_settings.png', 'bevakningar'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'vipstatus').'\')', 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>


	<div class="centerMenuBodyWhite">
		<form name="pres" action="<?=l('member', 'settings', 'fact')?>" method="post" onsubmit="if(TC_active) TC_VarToHidden();">
		<input type="hidden" name="do" value="1" />

		<div style="padding: 5px;">
			<script type="text/javascript">var alreadyupl = 0;</script>
			<table summary="" cellspacing="0" width="580"><tr>
				<td colspan="2" class="pdg">
					<table summary="" cellspacing="0"><tr>
						<td class="pdg_t" style="padding-right: 10px;"><b>Civilstånd:</b><br /><?=makeSelection('civil', $head['det_civil'][1])?></td>	
						<td class="pdg_t" style="padding-right: 6px;"><b>Attityd:</b><br /><?=makeSelection('attitude', $head['det_attitude'][1])?></td>
						<td class="pdg_t" style="padding-right: 6px;"><b>Barn:</b><br /><?=makeSelection('children', $head['det_children'][1])?></td>
					</tr><tr>
						<td class="pdg_t" style="padding-right: 6px;"><b>Alkohol:</b><br /><?=makeSelection('alcohol', $head['det_alcohol'][1])?></td>
						<td class="pdg_t" style="padding-right: 6px;"><b>Tobak:</b><br /><?=makeSelection('tobacco', $head['det_tobacco'][1])?></td>
						<td class="pdg_t" style="padding-right: 6px;"><b>Sexliv:</b><br /><?=makeSelection('sex', $head['det_sex'][1])?></td>
					</tr><tr>
						<td class="pdg_t" style="padding-right: 6px;"><b>Musiksmak:</b><br /><?=makeSelection('music', $head['det_music'][1])?></td>
						<td class="pdg_t" style="padding-right: 6px;"><b>Längd:</b><br /><?=makeSelection('length', $head['det_length'][1])?></td>
						<td class="pdg_t"><b>Vill ha:</b><br /><input type="text" class="txt" style="width: 185px;" name="det_wants" value="<?=@secureOUT($head['det_wants'][1])?>" /></td>
					</tr></table>
				</td>
			</tr></table>
		</div>
		<input type="submit" value="spara!" class="btn2_sml r" /><br class="clr"/>
		</form>
	</div>
</div>
	
<?
	include(DESIGN.'foot.php');
?>