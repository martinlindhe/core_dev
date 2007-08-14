<?
	if(!empty($_GET['a'])) $a = intval($_GET['a']); else $a = 0;		//detta är ett _SVAR_ på ett mail

	require(CONFIG.'secure.fnc.php');

	$error = false;

	if (!empty($_POST['text_html']) && !empty($_POST['ins_to']))
	{
		if (sendMail($_POST['ins_to'], $_POST['ins_cc'], $_POST['ins_ttl'], $_POST['text_html'], NRMSTR, $a)) {
			popupACT('Meddelande skickat!', '', '', '500');
		} else {
			$res = array('sent_ttl' => $_POST['ins_ttl'], 'sent_cmt' => $_POST['text_html'], 'u_alias' => $_POST['ins_to']);
		}
	}

	if (empty($_GET['id'])) {
		$s = false;
	} else {
		$s = (!is_numeric($_GET['id']))?false:$user->getuser($_GET['id']);
	}
	if ($a) {
		
		$ans = getMail($a);

		if(!empty($ans) && count($ans)) {
			if($ans['user_id'] == $l['id_id'] && $ans['status_id'] == '1' || $ans['sender_id'] == $l['id_id'] && $ans['sender_status'] == '1') {

			} else $a = false;
		} else $a = false;
	}

	if (isset($_GET['r']) && $a) $r = true; else $r = false;

	$NAME_TITLE = 'BREV - SKRIV | '.NAME_TITLE;
	$friends = getUserFriends();
	$fri = '';
	foreach($friends as $friend) {
		 $fri .= '<option value="'.secureOUT($friend['u_alias']).'">'.secureOUT($friend['u_alias'].' '.$sex[$friend['u_sex']].$user->doage($friend['u_birth'], 0)).'</option>';
	}
	require(DESIGN.'head_popup.php');


	//detect browser. only show advanced js-editor for IE & Firefox users. it is known to cause problems for Safari and other browsers--Martin
	
	$js_editor = false;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($user_agent, 'Firefox') !== FALSE) $js_editor = true; //aktiverad för Firefox
	if (strpos($user_agent, 'MSIE') !== FALSE) $js_editor = true;		//aktiverad för IE

	//if ($_SERVER['REMOTE_ADDR'] == '213.80.11.162') $js_editor = false;

	if ($js_editor) {
		echo '<body onload="TC_Init();" style="background: #FFF; padding-left: 6px;">';
	} else {
		echo '<body style="background: #FFF; padding-left: 6px;">';
	}
?>
	
<script type="text/javascript" src="<?=OBJ?>text_control.js"></script>
<script type="text/javascript">
function addselOption(txt, file) {
	len = document.getElementById('photo_list').options.length;
	document.getElementById('photo_list').options[len] = new Option(txt, file);
}
function fillField(val, obj) {
	if(val != '0')
		obj.value = val;
}
function cleanField(obj) {
	obj.value = obj.value.replace(',', '');
	obj.value = obj.value.replace(' ', '');
	obj.value = obj.value.replace(';', '');
}
</script>
<?
	$extra = '';
	if ($a) $extra = '&a='.$a;

if ($js_editor) { ?>
	<form name="mail_write" action="<?=l('user', 'mailwrite', $extra)?>" method="post" onsubmit="if(this.ins_to.value.length > 0) { if(TC_active) TC_VarToHidden(); } else { alert('Felaktigt fält: Till'); return false; }">
<? } else { ?>
	<form name="mail_write" action="<?=l('user', 'mailwrite', $extra)?>" method="post"">
<? } ?>
<input type="hidden" name="do" value="1"/>
<table summary="" cellspacing="0" width="99%" style="" class="cnti">
<tr>
	<td class="pdg bld">Från:</td>
	<td class="pdg" style="padding-left: 0; width: 100%;"><span class="nrm"><?=secureOUT($l['u_alias'])?></span></td>
	<td class="pdg bld rgt nobr"><?=(@$res['sent_date'])?nicedate($res['sent_date']):nicedate(date("Y-m-d"), 5);?></td>
</tr>
<tr>
	<td class="pdg bld" style="padding-top: 9px;">Till:</td>
	<td colspan="2" class="pdg_t" style="padding-left: 0;"><input type="text" class="txt" onkeyup="cleanField(this);" onchange="cleanField(this);" onkeydown="cleanField(this);" name="ins_to" style="width: 154px;" value="<?=($s)?((!$r)?@secureOUT($s['u_alias']):''):((!$r)?@secureOUT($res['u_alias']):'');?>"/>
	<select style="margin-left: 10px;" onchange="fillField(this.value, this.form.ins_to);">
	<option value="0">välj vän</option>
	<?=$fri?>
	</select>
	</td>
</tr>
<tr>
	<td class="pdg bld" style="padding-top: 9px;">Kopia:</td>
	<td colspan="2" class="pdg_t" style="padding-left: 0;"><input type="text" class="txt" onkeyup="cleanField(this);" onchange="cleanField(this);" onkeydown="cleanField(this);" name="ins_cc" style="width: 154px;" value=""/>
	<select style="margin-left: 10px;" onchange="fillField(this.value, this.form.ins_cc);">
	<option value="0">välj vän</option>
	<?=$fri?>
	</select>
	</td>
</tr>
<tr>
	<td class="pdg bld" style="padding-top: 9px; padding-right: 9px;">Rubrik:</td>
	<td colspan="2" class="pdg_t" style="padding-left: 0;"><input type="text" class="txt" name="ins_ttl" style="width: 296px;" value="<?=(@$res['sent_ttl'])?@secureOUT($res['sent_ttl']):(($a)?(($r)?'Vb: ':'Sv: ').$ans['sent_ttl']:'')?>"/></td>
</tr>
<tr>
	<td colspan="3" class="pdg_t" style="height: 90%;">
<table summary="" cellspacing="0" width="100%" style="height: 100%; display: none;" id="text_c_html" class="wht">
<tr><td class="pdg"><textarea name="text_html" id="text_html" style="width: 100%; height: 100%; padding: 10px; font-family: Courier New, Courier; font-size: 12px;">
<?
	if (!empty($res['sent_ttl'])) {
		echo secureOUT($res['sent_cmt']);
	} else {
		if ($a) {
			//print_r($ans);
			echo secureOUT(makeNR($ans['sent_cmt'], getUserName($ans['sender_id']), nicedate($ans['sent_date'], 4), getUserName($ans['user_id'])));
		}
	}
?>
</textarea></td></tr>
</table>

<? if ($js_editor) { ?>
	<table summary="" cellspacing="0" width="100%" style="" class="wht" id="text_c_var">
	<tr><td class="pdg" style="padding-bottom: 0;"><b>Design:&nbsp;</b>
	
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
		if(!border) border = 'solid #927750 1px';
		else border = 'solid #FFF 1px';
		obj.childNodes[0].style.border = border;
	}
	</script>
	<style type="text/css">
	.brrd { display: block; float: left; height: 20px; width: 22px; background-position: 0 1px; background-repeat: no-repeat; }
	.brrd img { border: 1px solid #927750; }
	</style>
	
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_bold.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('bold');" title="Fet"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_italic.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('italic');" title="Kursiv"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_underl.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('underline');" title="Understruken"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justl.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyleft');" title="Vänsterjustera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justc.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifycenter');" title="Centrera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justr.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyright');" title="Högerjustera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_justm.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('justifyfull');" title="Marginaljustera"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_ol.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('insertorderedlist');" title="Numrerad lista"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_ul.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('insertunorderedlist');" title="Punktlista"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_out.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('outdent');" title="Minska indrag"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_in.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('indent');" title="Öka indrag"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" style="margin-right: 10px;" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_hr.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('inserthorizontalrule');" title="Horisontell linje"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	<a class="cur brrd" style="background-image: url('<?=OBJ?>icon_remove.gif');" onmouseover="omo(this, 1);" onmouseout="omo(this); omd(this, '');" onmousedown="omd(this, '#c2b091');" onmouseup="omd(this, '');" onclick="javascript:TC_Format('removeformat');" title="Töm formatering"><img src="<?=OBJ?>1x1.gif" alt="" width="20" height="20" /></a>
	</div>
	</td></tr>
	<tr><td class="pdg" colspan="3"><div style="height: 100%; background: #FFF; border: 1px solid #999;"><iframe id="text_var" name="text_var" border="0" frameborder="0" width="100%" height="250"></iframe>
		</div></td></tr>
	</table>
	</td></tr>
	<tr>
		<td colspan="3" class="pdg"><input type="submit" class="btn2_sml r" value="skicka!" style="margin-top: 5px;" /><br class="clr" /></td>
	</tr>
	</table>

<?
	} else {
		echo '<textarea name="text_html" cols="50" rows="10"></textarea><br/><br/>';
		echo '<input type="submit" class="btn2_sml" value="skicka!"/>';
	} /* end if js_editor */
?>


<script type="text/javascript">
	<?=($error?'alert(\''.$error.'\');':'')?>
</script>
</form>
</body>
</html>