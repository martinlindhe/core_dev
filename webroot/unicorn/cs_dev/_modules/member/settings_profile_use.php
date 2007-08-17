<?
	require(CONFIG.'cut.fnc.php');
	require(CONFIG.'secure.fnc.php');
	$head = $user->getcontent($l['id_id'], 'user_head');

	if(!empty($_POST['do'])) {
#print_r($_POST['text_html']);
		if($l['status_id'] == '1') {
		if(isset($_POST['text_html'])) {
			if(substr($_POST['text_html'], 0, 6) == '&nbsp;') $_POST['text_html'] = substr($_POST['text_html'], 6);
			if(substr($_POST['text_html'], 0, 1) == ' ') $_POST['text_html'] = substr($_POST['text_html'], 1);
			$_POST['text_html'] = strip_tags($_POST['text_html'], NRMSTR);
			#$_POST['text_html'] = str_replace('<a href', '<a target="_blank" href', $_POST['text_html']);
			$id = $user->setinfo($l['id_id'], 'user_pres', @$_POST['text_html']);
			if($id[0]) $user->setrel($id[1], 'user_profile', $l['id_id']);
		}
		if(isset($_POST['ins_color'])) {
			$id = $user->setinfo($l['id_id'], 'user_pres_color', @substr($_POST['ins_color'], 0, 10));
			if($id[0]) $user->setrel($id[1], 'user_profile', $l['id_id']);
		}
		if(isset($_POST['ins_back'])) {
			$id = $user->setinfo($l['id_id'], 'user_pres_back', @substr($_POST['ins_back'], 0, 10));
			if($id[0]) $user->setrel($id[1], 'user_profile', $l['id_id']);
		}
		if(isset($_POST['ins_music'])) {
			$id = $user->setinfo($l['id_id'], 'user_music', @$_POST['ins_music']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}


		if(isset($_POST['det_opt']) && @$head['det_civil'][1] != $_POST['det_opt']) {
			$id = $user->setinfo($l['id_id'], 'det_civil', @$_POST['det_opt']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det']) && @$head['det_civil_type'][1] != $_POST['det']) {
			$id = $user->setinfo($l['id_id'], 'det_civil_type', @$_POST['det']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
			$string = $sql->queryResult("SELECT level_id FROM s_userlevel WHERE id_id = '".$l['id_id']."' LIMIT 1");
			if(!empty($head['det_civil_type'][1])) {
				$string = str_replace(' SINGLE'.((@$head['det_civil_type'][1] == 's')?'YES':'NO'), '', $string);
			}
			$string = $string.' SINGLE'.(($_POST['det'] == 's')?'YES':'NO');
			$sql->queryUpdate("UPDATE s_userlevel SET level_id = '$string' WHERE id_id = '".$l['id_id']."' LIMIT 1");
		}
		if(isset($_POST['det_music']) && @$head['det_music'][1] != $_POST['det_music']) {
			$id = $user->setinfo($l['id_id'], 'det_music', @$_POST['det_music']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_drink']) && @$head['det_drink'][1] != $_POST['det_drink']) {
			$id = $user->setinfo($l['id_id'], 'det_drink', @$_POST['det_drink']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_idol']) && @$head['det_idol'][1] != $_POST['det_idol']) {
			$id = $user->setinfo($l['id_id'], 'det_idol', @$_POST['det_idol']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		if(isset($_POST['det_wants']) && @$head['det_wants'][1] != $_POST['det_wants']) {
			$id = $user->setinfo($l['id_id'], 'det_wants', @$_POST['det_wants']);
			if($id[0]) $user->setrel($id[1], 'user_head', $l['id_id']);
		}
		}

		if(!empty($_POST['go']))
			reloadACT(l('user', 'view'));
		else
			errorTACT('Uppdaterat!', l('member', 'settings', 'profile'), 1500);
	}
	$result = $sql->query("SELECT main_id, status_id, picd, hidden_id, hidden_value, pht_name, pht_cmt FROM s_userphoto WHERE user_id = '".$l['id_id']."' AND status_id = '1' ORDER BY main_id DESC");
	$friends = $sql->query("SELECT rel.main_id, rel.user_id, rel.rel_id, u.id_id, u.u_alias, u.u_picvalid, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth FROM s_userrelation rel RIGHT JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = '1' WHERE rel.user_id = '".secureINS($l['id_id'])."' ORDER BY u.u_alias ASC", 0, 1);
	$page = 'settings_profile';
	$profile = $user->getcontent($l['id_id'], 'user_profile');

	require(DESIGN.'head.php');
?>
<script type="text/javascript">var alreadyupl = 0;</script>
		<div id="contentWhole" style="margin-left: 10px;">
<div class="boxBig1">
	<img src="<?=CS?>_objects/_heads/head_settings.png" style="position: absolute; top: -10px; left: -10px;" />
	<div style="position: absolute; top: 0; left: 165px;"><?=makeMenu($page, $menu)?></div>
	<div class="boxBig1mid">
<script type="text/javascript" src="<?=OBJ?>text_control.js"></script>
<script type="text/javascript">
function addselOption(txt, file) {
	len = document.getElementById('photo_list').options.length;
	document.getElementById('photo_list').options[len] = new Option(txt, file);
}
window.onload = function() { TC_Init(); }
</script>
<script>
function makeDialog(url, w, h){
if(document.all && window.print)
	window.showModalDialog(url, 'dialog_alias','help: 0; resizable: 0; dialogWidth: ' + w + 'px; dialogHeight: ' + h + 'px;');
else
	window.open(url, 'dialog_alias', 'width='+w+'px, height='+h+'px, resizable=0, scrollbars=0');
}
</script>
<form name="pres" action="<?=l('member', 'settings', 'profile')?>" method="post" onsubmit="if(TC_active) TC_VarToHidden();">
	<input type="hidden" name="do" value="1" />
<?=(!empty($_GET['go']))?'<input type="hidden" name="go" value="1" />':'';?>

<table cellspacing="0" width="900" style="margin-top: 38px;">
<tr>
	<td colspan="2" class="pdg">

	<table cellspacing="0">
	<tr>
		<td class="pdg_t" style="padding-right: 10px;"><b>Civilstatus:</b><br /><nobr style="display: block; margin-top: 3px;">
<input type="radio" name="det" class="chk" id="s" value="s"<?=(@$head['det_civil_type'][1] == 's')?' checked':'';?>><label for="s"> Singel</label>
<input type="radio" name="det" class="chk" id="f" value="f"<?=(@$head['det_civil_type'][1] == 'f')?' checked':'';?>><label for="f"> Flexibel</label>
<input type="radio" name="det" class="chk" id="b" value="b"<?=(@$head['det_civil_type'][1] == 'b')?' checked':'';?>><label for="b"> Upptagen</label></nobr></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Beskrivning:</b><br /><input type="text" class="txt" name="det_opt" value="<?=@secureOUT($head['det_civil'][1])?>" /></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Musiksmak:</b><br /><input type="text" class="txt" name="det_music" value="<?=@secureOUT($head['det_music'][1])?>" /></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Favoritdryck:</b><br /><input type="text" class="txt" name="det_drink" value="<?=@secureOUT($head['det_drink'][1])?>" /></td>
		<td class="pdg_t" style="padding-right: 6px;"><b>Idol:</b><br /><input type="text" class="txt" name="det_idol" value="<?=@secureOUT($head['det_idol'][1])?>" /></td>
		<td class="pdg_t"><b>Vill ha:</b><br /><input type="text" class="txt" name="det_wants" value="<?=@secureOUT($head['det_wants'][1])?>" /></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td class="pdg rgt" colspan="4"><input type="submit" class="b" value="spara"></td>
</tr>
</table>
<table cellspacing="0" width="920" class="mrg_t ">
<tr>
	<td class="pdg" colspan="2">
<table cellspacing="0" width="100%" style="display: none;" id="text_c_html">
<tr><td><textarea name="text_html" id="text_html" style="width: 900px; height: 517px; padding: 10px; font-family: Courier New, Courier; font-size: 12px;">&nbsp;<?=@secureFormat($profile['user_pres'][1])?></textarea></td></tr>
</table>
<table cellspacing="0" width="100%" class="mrg_t" id="text_c_var">
<tr><td style="padding: 0 0 5px 0;"><b>Design:&nbsp;</b>

<select onchange="if(this.value) { TC_Format('FontName', this.value); this.selectedIndex = 0; }">
<option value="0">Typsnitt</option>
<option value="Arial Black">Arial Black</option>
<option value="Arial Narrow">Arial Narrow</option>
<option value="Arial">Arial</option>
<option value="Comic Sans MS">Comic Sans MS</option>
<option value="Courier New">Courier New</option>
<option value="Courier">Courier</option>
<option value="Georgia">Georgia</option>
<option value="Helvetica">Helvetica</option>
<option value="Impact">Impact</option>
<option value="Lucida Blackletter">Lucida Blackletter</option>
<option value="Lucida Calligraphy">Lucida Calligraphy</option>
<option value="Lucida Sans Typewriter">Lucida Sans Typewriter</option>
<option value="Lucida Sans">Lucida Sans</option>
<option value="OCR-A">OCR-A</option>
<option value="Times New Roman">Times New Roman</option>
<option value="Trebuchet MS">Trebuchet MS</option>
<option value="Verdana">Verdana</option>
</select>
<select style="width: 60px;" onchange="if(this.value) { TC_Format('FontSize', this.value); this.selectedIndex = 0; }">
<option value="0">Storlek</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
</select>
<select style="width: 100px;" onchange="if(this.value) { TC_Format('ForeColor', this.value); this.selectedIndex = 0; }">
<option value="0">Textfärg</option>
<option value="FFFFFF" style="background: #FFFFFF;">&nbsp;</option>
<option value="000000" style="background: #000000;">&nbsp;</option>
<option value="FF8080" style="background: #FF8080;">&nbsp;</option>
<option value="FFFF80" style="background: #FFFF80;">&nbsp;</option>
<option value="80FF80" style="background: #80FF80;">&nbsp;</option>
<option value="00FF80" style="background: #00FF80;">&nbsp;</option>
<option value="80FFFF" style="background: #80FFFF;">&nbsp;</option>
<option value="0080FF" style="background: #0080FF;">&nbsp;</option>
<option value="FF80C0" style="background: #FF80C0;">&nbsp;</option>
<option value="FF80FF" style="background: #FF80FF;">&nbsp;</option>
<option value="FF0000" style="background: #FF0000;">&nbsp;</option>
<option value="FFFF00" style="background: #FFFF00;">&nbsp;</option>
<option value="80FF00" style="background: #80FF00;">&nbsp;</option>
<option value="00FF40" style="background: #00FF40;">&nbsp;</option>
<option value="00FFFF" style="background: #00FFFF;">&nbsp;</option>
<option value="0080C0" style="background: #0080C0;">&nbsp;</option>
<option value="8080C0" style="background: #8080C0;">&nbsp;</option>
<option value="FF00FF" style="background: #FF00FF;">&nbsp;</option>
<option value="804040" style="background: #804040;">&nbsp;</option>
</select>
<select style="width: 100px;" onchange="if(this.value) { TC_Format('BackColor', this.value); this.selectedIndex = 0; }">
<option value="0">Bakgrundsfärg</option>
<option value="FFFFFF" style="background: #FFFFFF;">&nbsp;</option>
<option value="000000" style="background: #000000;">&nbsp;</option>
<option value="FF8080" style="background: #FF8080;">&nbsp;</option>
<option value="FFFF80" style="background: #FFFF80;">&nbsp;</option>
<option value="80FF80" style="background: #80FF80;">&nbsp;</option>
<option value="00FF80" style="background: #00FF80;">&nbsp;</option>
<option value="80FFFF" style="background: #80FFFF;">&nbsp;</option>
<option value="0080FF" style="background: #0080FF;">&nbsp;</option>
<option value="FF80C0" style="background: #FF80C0;">&nbsp;</option>
<option value="FF80FF" style="background: #FF80FF;">&nbsp;</option>
<option value="FF0000" style="background: #FF0000;">&nbsp;</option>
<option value="FFFF00" style="background: #FFFF00;">&nbsp;</option>
<option value="80FF00" style="background: #80FF00;">&nbsp;</option>
<option value="00FF40" style="background: #00FF40;">&nbsp;</option>
<option value="00FFFF" style="background: #00FFFF;">&nbsp;</option>
<option value="0080C0" style="background: #0080C0;">&nbsp;</option>
<option value="8080C0" style="background: #8080C0;">&nbsp;</option>
<option value="FF00FF" style="background: #FF00FF;">&nbsp;</option>
<option value="804040" style="background: #804040;">&nbsp;</option>
</select>
<div style="margin: 4px 0 0 48px;">
<script type="text/javascript">
function omd(obj, color) {
	obj.style.backgroundColor = color;
}
function omo(obj, border) {
	if(!border) border = 'solid #c5bf70 1px';
	else border = 'solid #FFF 1px';
	obj.childNodes[0].style.border = border;
}
</script>
<style type="text/css">
.brrd { display: block; float: left; height: 20px; width: 22px; background-position: 0 1px; background-repeat: no-repeat; }
.brrd img { border: 1px solid #c5bf70; }
</style>

<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_bold.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('bold');" title="Fet"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_italic.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('italic');" title="Kursiv"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_underl.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('underline');" title="Understruken"><img src="<?=OBJ?>1x1.gif" width="20" height="20" style="margin-right: 10px;" /></a>

<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justl.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyleft');" title="Vänsterjustera"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justc.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifycenter');" title="Centrera"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justr.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyright');" title="Högerjustera"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justm.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyfull');" title="Marginaljustera"><img src="<?=OBJ?>1x1.gif" width="20" height="20" style="margin-right: 10px;" /></a>

<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_ol.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('insertorderedlist');" title="Numrerad lista"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_ul.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('insertunorderedlist');" title="Punktlista"><img src="<?=OBJ?>1x1.gif" width="20" height="20" style="margin-right: 10px;" /></a>

<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_out.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('outdent');" title="Minska indrag"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_in.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('indent');" title="Öka indrag"><img src="<?=OBJ?>1x1.gif" width="20" height="20" style="margin-right: 10px;" /></a>

<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_hr.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('inserthorizontalrule');" title="Horisontell linje"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_remove.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('removeformat');" title="Töm formatering"><img src="<?=OBJ?>1x1.gif" width="20" height="20" /></a>
</div>
<div style="clear: both; margin: 10px 0 0 0;">
	<b>Foto:&nbsp;</b><select id="photo_list" name="photo_list" style="width: 142px; margin: 0 5px 0 17px;" onchange="if(TC_initialized) { if(this.value) { if(this.options[this.selectedIndex].innerHTML.indexOf('[privat]') == -1 || confirm('Denna bild är markerad som privat! Den kommer att visas för alla som läser om du lägger in den i ditt meddelande.')) { TC_Format('InsertImage', this.value); } else editor.focus(); } } this.selectedIndex = 0;">
	<option value="0">Välj bild från fotoalbum</option>
<?
	foreach($result as $pic) {
		echo '<option value="'.P2B.USER_GALLERY.$pic[2].'/'.$pic[0].(($pic[3])?'_'.$pic[4]:'').'.'.$pic[5].'">'.secureOUT('#'.$pic[0].' - '.$pic[6]).' '.(($pic[3])?'<b>[privat]</b>':'').'</option>';
	}
?>
	</select><input type="button" class="b" value="ladda upp ny" onclick="makePop('user_photo_upload.php?type=blog', 'upload', 350, 450, 1, ',scrollbars=0', 'resizable=0, status=1, '); return false;" style="margin-right: 10px;">

<script type="text/javascript">
function replaceALIAS(id) {
	alias = id.split(':::');
	id = alias[0];
	alias = alias[1].split(' ');
	alias = alias[0];
	n_id = id.split('/');
	n_id = n_id[n_id.length-1];
	n_id = n_id.substr(0, 32);
	TC_VarToHidden();
	str = '<IMG src="' + id + '">';
	str2 = '<img src="' + id + '">';
	_d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace(str, '<a href="<?=P2B?>user/view/' + n_id + '" title="' + alias + '"><img alt="' + alias + '" src="' + id + '" alt="" /></a>');
	_d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace(str2, '<a href="./user.php?id=' + n_id + '" title="' + alias + '"><img alt="' + alias + '" src="' + id + '" alt="" /></a>');
	//_d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace(/_blank/gi, 'commain');
	TC_HiddenToVar();
}

var actID = '';
</script>
	<b>Länk till vän med bild:&nbsp;</b><select id="friend_list" name="friend_list" style="margin: 0 5px 0 17px;" onchange="if(TC_initialized) { if(this.value) { TC_Format('InsertImage', this.value); actName = this[this.selectedIndex].innerHTML; actID = this.value + ':::' + actName; window.setTimeout('replaceALIAS(actID)', 100); } else editor.focus(); } this.selectedIndex = 0;">
	<option value="0">Välj vän</option>
<?
	foreach($friends as $friend) {
		if($friend['u_picvalid'] == '1') {
			$pic = P2B.USER_FIMG.$friend['id_id'].'.jpg';
			echo '<option value="'.$pic.'">'.secureOUT($friend['u_alias'].' '.$sex[$friend['u_sex']].$user->doage($friend['u_birth'], 0)).'</option>';
		}
	}
?>
	</select>
</div>
</td></tr>
<tr><td colspan="2" style="border: 1px solid #999; height: 100%; background: #FFF;"><iframe id="text_var" name="text_var" border="0" style="cursor: text" frameborder="0" width="100%" height="700"></iframe></td></tr>
</table>
	</td>
</tr>
<tr>
	<td class="pdg" colspan="2"><a class="cur bld" onclick="TC_Switch();">text/HTML</a></td>
</tr>
</table>
</div>
	<input type="image" style="position: absolute; right: -10px; bottom: -11px;" src="<?=OBJ?>_heads/btn1_save.png" />
	</form>
		</div>
	</div>
<?
	include(DESIGN.'foot.php');
?>