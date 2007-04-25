<?
	include(CONFIG.'secure.fnc.php');

	$NAME_TITLE = 'BLOGG - SKRIV | '.NAME_TITLE;
	$edit = false;
	$gotone = true;
	$lim = 5;

	if(!empty($_GET['i'])) {
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, blog_title, blog_date, blog_cmt, hidden_id FROM {$t}userblog WHERE main_id = '".secureINS($_GET['i'])."' LIMIT 1", 1);
		if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $res['user_id'] != $l['id_id']) {
			#popupACT('Felaktigt inlägg.');
			$gotone = false;
		} else $edit = true;
	}

	if(!empty($_POST['do']) && !empty($_POST['text_html'])) {


		$hidden = (!empty($_POST['ins_priv']) && $user->level($l['level_id'], 2))?'1':'0';
		$_POST['text_html'] = strip_tags($_POST['text_html'], NRMSTR);
		if($edit) {
			$sql->queryUpdate("UPDATE {$t}userblog SET blog_cmt = '".@secureINS($_POST['text_html'])."', hidden_id = '$hidden', blog_title = '".@secureINS($_POST['ins_title'])."' WHERE main_id = '".$res['main_id']."' LIMIT 1");
			$id = $res['main_id'];
		} else {
			$id = $sql->queryInsert("INSERT INTO {$t}userblog SET blog_idx = NOW(), user_id = '".$l['id_id']."', hidden_id = '$hidden', blog_cmt = '".@secureINS($_POST['text_html'])."', blog_title = '".@secureINS($_POST['ins_title'])."', blog_date = NOW()");
#			$res = $sql->query("SELECT p.user_id FROM {$t}userblogspy p INNER JOIN {$t}user u ON u.id_id = p.user_id AND u.status_id = '1' WHERE p.blogger_id = '".$l['id_id']."' AND p.status_id = '1'");
			$user->counterIncrease('blog', $l['id_id']);
#			if(!$hidden) foreach($res as $row) if($row[0] != $l['id_id']) $user->spy($row[0], $id, 'BLG', array($l['u_alias']));
		}
		if(isset($_GET['m']))
			popupACT('Publicerad!', '', '1000', 'user_blog.php?'.mt_rand(1000, 9999).'#R'.$id);
		elseif(isset($_GET['d']))
			popupACT('Publicerad!', '', '1000', 'user_blog.php?date='.secureOUT($_GET['d']).'&amp;'.mt_rand(1000, 9999).'&amp;#R'.$id);
		else
			popupACT('Publicerad!', '', l('user', 'blog', $l['id_id'], $id), 1000);
	}

	if($edit) {
#		$linked = $sql->query("SELECT photo_id FROM {$tab['bloglink']} WHERE diary_id = '".$res['main_id']."' ORDER BY main_id ASC LIMIT $lim");
	} elseif(!$gotone) {
		popupACT('Felaktigt inlägg.');
	}

	$result = $sql->query("SELECT main_id, status_id, picd, hidden_id, hidden_value, pht_name, pht_cmt FROM {$t}userphoto WHERE user_id = '".$l['id_id']."' AND status_id = '1' ORDER BY main_id DESC");
	require(DESIGN.'head_popup.php');
?>

<script type="text/javascript" src="<?=OBJ?>text_control.js"></script>
<script type="text/javascript">
window.onload = TC_Init;
function addselOption(txt, file) {
	len = document.getElementById('photo_list').options.length;
	document.getElementById('photo_list').options[len] = new Option(txt, file);
}
function validateIt(tForm) {
	if(tForm.ins_title.value == '')	{
		alert('Du måste skriva en rubrik.');
		tForm.ins_title.focus();
		return false;
	}
	if(TC_active) TC_VarToHidden();
	return true;
}
</script>

<form name="blog_write" action="<?=l('user', 'blog', $l['id_id'], '0')?>write=1&amp;i=<?=isset($_GET['m'])?'&amp;m':((isset($_GET['d']))?'&amp;d='.secureOUT($_GET['d']):'&amp;n');?><?=($edit)?'&amp;i='.$res['main_id']:'';?>" method="post" onsubmit="return validateIt(this);">
<input type="hidden" name="do" value="1"/>

<div class="boxMid4" style="margin: 25px 15px 0 15px;">
		<img src="/_gfx/ttl_blog.png" alt="Blogg"/><br/><br/>
	<div class="boxMid4mid">

<table summary="" cellspacing="0" width="510" style="height: 400px; margin-top: 38px; margin-bottom: 1px;" class="lft">
<tr>
	<td class="pdg bld"><b>Rubrik:&nbsp;</b><input type="text" class="txt" name="ins_title" style="width: 296px; margin-left: 6px; margin-bottom: -4px;" value="<?=@secureOUT($res['blog_title'])?>"/></td>
	<td class="pdg bld rgt"><?=(@$res['blog_date']?nicedate($res['blog_date']):'');?></td>
</tr>
<tr>
	<td colspan="2" class="pdg" style="height: 100%; padding-top: 0;">
<table summary="" cellspacing="0" width="100%" style="height: 100%; display: none;" id="text_c_html">
<tr><td><textarea name="text_html" id="text_html" style="width: 510px; height: 100%; padding: 10px; font-family: Courier New, Courier; font-size: 12px;"><?=@secureFormat($res['blog_cmt'])?></textarea></td></tr>
</table>
<table summary="" cellspacing="0" width="100%" style="height: 100%;" class="mrg_t" id="text_c_var">
<tr><td style="padding-bottom: 5px;"><b>Design:&nbsp;</b>

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
	if(!border) border = 'solid #7ca66a 1px';
	else border = 'solid #FFF 1px';
	obj.childNodes[0].style.border = border;
}
</script>
<style type="text/css">
.brrd { display: block; float: left; height: 20px; width: 22px; background-position: 0 1px; background-repeat: no-repeat; }
.brrd img { border: 1px solid #7ca66a; }
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
<blockquote style="clear: both; margin: 10px 0 0 0;">
<b>Foto:&nbsp;</b><select id="photo_list" name="photo_list" style="width: 142px; margin: 0 5px 0 17px;" onchange="if(TC_initialized) { if(this.value) { if(this.options[this.selectedIndex].innerHTML.indexOf('[privat]') == -1 || confirm('Denna bild är markerad som privat! Den kommer att visas för alla som läser om du lägger in den i ditt meddelande.')) { TC_Format('InsertImage', this.value); } else editor.focus(); } } this.selectedIndex = 0;">
<option value="0">Välj bild från fotoalbum</option>
<?
	foreach($result as $pic) {
		echo '<option value="'.P2B.USER_GALLERY.$pic[2].'/'.$pic[0].(($pic[3])?'_'.$pic[4]:'').'.'.$pic[5].'">'.secureOUT('#'.$pic[0].' - '.$pic[6]).' '.(($pic[3])?'[privat]':'').'</option>';
	}
?>
</select><input type="button" class="btn2_med" value="ladda upp ny" onclick="makeUpload('<?=$l['id_id']?>&amp;do=blog'); return false;"/>
</blockquote>
</td></tr>
<tr><td colspan="2" style="border: 1px solid #999; height: 100%; background: #FFF;"><iframe id="text_var" style="cursor: text" name="text_var" border="0" frameborder="0" width="100%" height="100%"></iframe></td></tr>
</table>
</td></tr>
<tr>
	<td class="pdg" colspan="2"><input type="checkbox" class="chk" value="1" name="ins_priv" id="ins_priv"<?=($edit && $res['hidden_id'])?' checked="checked"':'';?>/><label for="ins_priv"> Privat inlägg [endast för vänner]</label></td>
</tr>
</table>
	</div>
	<? makeButton(false, 'validateIt(blog_write); document.blog_write.submit();', 'icon_blog.png', 'skicka'); ?>
</div>
</form>
</body>
</html>