<?
	require(CONFIG.'cut.fnc.php');
	require(CONFIG.'secure.fnc.php');
	$head = $user->getcontent($l['id_id'], 'user_head');
	//print_r($head);
	//print_r( $head['det_drink']);
	if(!empty($_POST['do'])) {
#print_r($_POST['text_html']);
		if($l['status_id'] == '1') {
		if(isset($_POST['det_civil']) && (@$head['det_civil'][1] != $_POST['det_civil'] || !isset($head['det_civil'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_civil', "'".@secureINS($_POST['det_civil'])."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_attitude']) && (@$head['det_attitude'][1] != $_POST['det_attitude'] || !isset($head['det_attitude'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_attitude', "'".@secureINS($_POST['det_attitude'])."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_children']) && (@$head['det_children'][1] != $_POST['det_children'] || !isset($head['det_children'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_children', "'".@$_POST['det_children']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_alcohol']) && (@$head['det_alcohol'][1] != $_POST['det_alcohol'] || !isset($head['det_alcohol'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_alcohol', "'".@$_POST['det_alcohol']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_tobacco']) && (@$head['det_tobacco'][1] != $_POST['det_tobacco'] || !isset($head['det_tobacco'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_tobacco', "'".@$_POST['det_tobacco']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_sex']) && (@$head['det_sex'][1] != $_POST['det_sex'] || !isset($head['det_sex'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_sex', "'".@$_POST['det_sex']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_music']) && (@$head['det_music'][1] != $_POST['det_music'] || !isset($head['det_music'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_music', "'".@$_POST['det_music']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_length']) && (@$head['det_length'][1] != $_POST['det_length'] || !isset($head['det_length'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_length', "'".@$_POST['det_length']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_wants']) && (@$head['det_wants'][1] != $_POST['det_wants'] || !isset($head['det_wants'][1]))) {
			$id = $user->setinfo($l['id_id'], 'det_wants', "'".@$_POST['det_wants']."'");
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		}

		errorTACT('Uppdaterat!', l('member', 'settings', 'fact'), 1500);
	}
	$page = 'settings_fact';
	$profile = $user->getcontent($l['id_id'], 'user_profile');

$civil = getset('', 'civil', 'mo', 'text_cmt ASC');
$attitude = getset('', 'attitude', 'mo', 'text_cmt ASC');
$alcohol = getset('', 'alcohol', 'mo', 'text_cmt ASC');
$children = getset('', 'children', 'mo', 'text_cmt ASC');
$drink = getset('', 'drink', 'mo', 'text_cmt ASC');
$tobacco = getset('', 'tobacco', 'mo', 'text_cmt ASC');
$sex = getset('', 'sex', 'mo', 'text_cmt ASC');
$music = getset('', 'music', 'mo', 'text_cmt ASC');
$length = getset('', 'length', 'mo', 'text_cmt ASC');
function makeSelection($name, $array, $sel) {
	$ret = '<select style="width: 185px;" name="'.$name.'"><option value="">-- Välj --</option>';
	foreach($array as $arr) $ret .= '<option value="'.$arr[1].'"'.($sel == $arr[1]?' selected':'').'>'.$arr[1].'</option>';
	return $ret.'</select>';
}
	require(DESIGN.'head.php');
?>
	<div id="mainContent">
			<div class="mainHeader2"><h4>inställningar - <?=makeMenu($page, $menu)?></h4></div>
			<div class="mainBoxed2"><div style="padding: 5px;">
<script type="text/javascript">var alreadyupl = 0;</script>
<form name="pres" action="<?=l('member', 'settings', 'fact')?>" method="post" onsubmit="if(TC_active) TC_VarToHidden();">
	<input type="hidden" name="do" value="1" />
<table cellspacing="0" width="580">
<tr>
	<td colspan="2" class="pdg">

	<table cellspacing="0">
	<tr>
		<td class="pdg_t" style="padding-right: 10px;"><b>Civilstånd:</b><br /><?=makeSelection('det_civil', $civil, $head['det_civil'][1])?></td>	
		<td class="pdg_t" style="padding-right: 6px;"><b>Attityd:</b><br /><?=makeSelection('det_attitude', $attitude, $head['det_attitude'][1])?></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Barn:</b><br /><?=makeSelection('det_children', $children, $head['det_children'][1])?></td>
	</tr><tr>
		<td class="pdg_t" style="padding-right: 6px;"><b>Alkohol:</b><br /><?=makeSelection('det_alcohol', $alcohol, $head['det_alcohol'][1])?></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Tobak:</b><br /><?=makeSelection('det_tobacco', $tobacco, $head['det_tobacco'][1])?></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Sexliv:</b><br /><?=makeSelection('det_sex', $sex, $head['det_sex'][1])?></td>
		</tr><tr>
		<td class="pdg_t" style="padding-right: 6px;"><b>Musiksmak:</b><br /><?=makeSelection('det_music', $music, $head['det_music'][1])?></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Längd:</b><br /><?=makeSelection('det_length', $length, $head['det_length'][1])?></td>
		<td class="pdg_t"><b>Vill ha:</b><br /><input type="text" class="txt" style="width: 185px;" name="det_wants" value="<?=@secureOUT($head['det_wants'][1])?>" /></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
	<input type="submit" value="spara!" class="btn2_sml r" />
		</div>
	</div>
	</form>
<?
	include(DESIGN.'foot.php');
?>