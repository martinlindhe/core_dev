<?
	$head = $user->getcontent($l['id_id'], 'user_head');

	if(!empty($_POST['do'])) {
		if($l['status_id'] == '1' && isset($_POST['text_html'])) {
			if(substr($_POST['text_html'], 0, 6) == '&nbsp;') $_POST['text_html'] = substr($_POST['text_html'], 6);
			if(substr($_POST['text_html'], 0, 1) == ' ') $_POST['text_html'] = substr($_POST['text_html'], 1);
			$_POST['text_html'] = strip_tags($_POST['text_html'], NRMSTR);
			$id = $user->setinfo($l['id_id'], 'user_pres', @$_POST['text_html']);
			if($id[0]) $user->setrel($id[1], 'user_profile', $l['id_id']);
		}

		if(!empty($_POST['go']))
			reloadACT(l('user', 'view'));
		else
			errorTACT('Uppdaterat!', l('member', 'settings'), 1500);
	}
	$result = $db->getArray('SELECT main_id, status_id, picd, hidden_id, hidden_value, pht_name, pht_cmt FROM s_userphoto WHERE user_id = '.$l['id_id'].' AND status_id = "1" ORDER BY main_id DESC');
	$friends = $db->getArray('SELECT rel.main_id, rel.user_id, rel.rel_id, u.id_id, u.u_alias, u.u_picvalid, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth FROM s_userrelation rel RIGHT JOIN s_user u ON u.id_id = rel.friend_id AND u.status_id = "1" WHERE rel.user_id = '.secureINS($l['id_id']).' ORDER BY u.u_alias ASC');
	$page = 'settings_profile';
	$profile = $user->getcontent($l['id_id'], 'user_profile');

	require(DESIGN.'head.php');
?>
<script type="text/javascript">var alreadyupl = 0;</script>
<script type="text/javascript" src="<?=OBJ?>text_control.js"></script>
<script type="text/javascript">
function addselOption(txt, file) {
	len = document.getElementById('photo_list').options.length;
	document.getElementById('photo_list').options[len] = new Option(txt, file);
}
window.onload = function() { TC_Init(); }
function makeDialog(url, w, h){
if(document.all && window.print)
	window.showModalDialog(url, 'dialog_alias','help: 0; resizable: 0; dialogWidth: ' + w + 'px; dialogHeight: ' + h + 'px;');
else
	window.open(url, 'dialog_alias', 'width='+w+'px, height='+h+'px, resizable=0, scrollbars=0');
}
function omd(obj, color) {
	obj.style.backgroundColor = color;
}
function omo(obj, border) {
	if(!border) border = 'solid #ccc 1px';
	else border = 'solid #000 1px';
	obj.childNodes[0].style.border = border;
}

function replaceALIAS(id) {
	alias = id.split(':::');
	id = alias[0];
	alias = alias[1].split(' ');
	alias = alias[0];
	n_id = id.split('/');
	n_id = n_id[n_id.length-1];
	//n_id = n_id.substr(0, 32);
	n_id = n_id.split('.');
	n_id = n_id[0];
	TC_VarToHidden();
  _d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace('<img src="' + id + '">', '<a href="<?=P2B?>user/view/' + n_id + '" title="' + alias + '"><img alt="' + alias + '" src="' + id + '" alt="" /></a>');
  _d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace('<IMG src="' + id + '">', '<a href="<?=P2B?>user/view/' + n_id + '" title="' + alias + '"><img alt="' + alias + '" src="' + id + '" alt="" /></a>');
	//_d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace(str2, '<a href="./user.php?id=' + n_id + '" title="' + alias + '"><img alt="' + alias + '" src="' + id + '" alt="" /></a>');
	//_d.getElementById(TC_name + '_html').value = _d.getElementById(TC_name + '_html').value.replace(/_blank/gi, 'commain');
	TC_HiddenToVar();
}

var actID = '';
</script>

<style type="text/css">
.brrd { display: block; float: left; height: 20px; width: 22px; margin-right: 1px; background-position: 0 1px; background-repeat: no-repeat; }
.brrd img { border: 1px solid #ccc; }
</style>

<div id="mainContent">

	<div class="subHead">inställningar</div><br class="clr"/>

	<? makeButton(true, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'theme').'\')', 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'subscription').'\')', 'icon_settings.png', 'span'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'vipstatus').'\')', 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>


	<div class="centerMenuBodyWhite"><div style="padding: 5px;">
		<form name="pres" action="<?=l('member', 'settings')?>" method="post" onsubmit="if(TC_active) TC_VarToHidden();">
		<input type="hidden" name="do" value="1" />
		<?=(!empty($_GET['go']))?'<input type="hidden" name="go" value="1" />':'';?>
		<input type="submit" value="spara!" class="btn2_sml r" />
		<table summary="" cellspacing="0" width="580" class="mrg_t">
			<tr>
				<td class="pdg" colspan="2">
					<table summary="" cellspacing="0" width="100%" style="display: none;" id="text_c_html">
						<tr><td><textarea name="text_html" id="text_html" style="width: 550px; height: 317px; padding: 10px; font-family: Courier New, Courier; font-size: 12px;"> <?=@secureFormat($profile['user_pres'][1])?></textarea></td></tr>
					</table>
					<table summary="" cellspacing="0" width="100%" class="mrg_t" id="text_c_var">
						<tr><td style="padding: 0 0 5px 0;">
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
							<select style="width: 70px;" onchange="if(this.value) { TC_Format('FontSize', this.value); this.selectedIndex = 0; }">
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
							<div style="margin: 4px 0 0 0;">
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_bold.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('bold');" title="Fet"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_italic.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('italic');" title="Kursiv"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_underl.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('underline');" title="Understruken"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
									
								<a class="cur brrd" style="margin-left: 10px; background-image: url('<?=OBJ?>icon_justl.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyleft');" title="Vänsterjustera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justc.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifycenter');" title="Centrera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justr.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyright');" title="Högerjustera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justm.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyfull');" title="Marginaljustera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
									
								<a class="cur brrd" style="margin-left: 10px; background-image: url('<?=OBJ?>icon_ol.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('insertorderedlist');" title="Numrerad lista"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
								<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_ul.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('insertunorderedlist');" title="Punktlista"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
									
								<a class="cur brrd" style="margin-left: 10px; background-image: url('<?=OBJ?>icon_remove.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('removeformat');" title="Töm formatering"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
							</div>
							<div style="clear: both; margin: 10px 0 0 0;">
								<b>Foto:&nbsp;</b><select id="photo_list" name="photo_list" style="width: 142px; margin: 0 5px 0 17px;" onchange="if(TC_initialized) { if(this.value) { if(this.options[this.selectedIndex].innerHTML.indexOf('[privat]') == -1 || confirm('Denna bild är markerad som privat! Den kommer att visas för alla som läser om du lägger in den i ditt meddelande.')) { TC_Format('InsertImage', this.value); } else editor.focus(); } } this.selectedIndex = 0;">
								<option value="0">Välj bild från fotoalbum</option>
								<?
								foreach($result as $pic) {
									echo '<option value="'.P2B.USER_GALLERY.$pic[2].'/'.$pic[0].(($pic[3])?'_'.$pic[4]:'').'.'.$pic[5].'">'.secureOUT('#'.$pic[0].' - '.$pic[6]).' '.(($pic[3])?'[privat]':'').'</option>';
								}
								?>
								</select><input type="button" class="b" value="ladda upp ny" onclick="makeUpload('do=pres'); return false;" style="margin-right: 10px;"/>
								
								<b>Länk till vän med bild:&nbsp;</b><select id="friend_list" name="friend_list" style="margin: 0 5px 0 17px;" onchange="if(TC_initialized) { if(this.value) { TC_Format('InsertImage', this.value); actName = this[this.selectedIndex].innerHTML; actID = this.value + ':::' + actName; window.setTimeout('replaceALIAS(actID)', 200); } else editor.focus(); } this.selectedIndex = 0;">
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
						<tr><td colspan="2" style="border: 1px solid #999; height: 100%; background: #FFF;"><iframe id="text_var" name="text_var" border="0" style="cursor: text" frameborder="0" width="100%" height="300"></iframe></td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="pdg" colspan="2"><a class="cur bld" onclick="TC_Switch();">text/HTML</a></td>
			</tr>
		</table>
		<input type="submit" value="spara!" class="btn2_sml r" /><br class="clr"/>
		</form>
		
	</div>
	
</div>
</div>
	
<?
	include(DESIGN.'foot.php');
?>