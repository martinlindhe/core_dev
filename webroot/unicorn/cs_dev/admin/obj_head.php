<?
	require('admin_head.php');
?>
<script type="text/javascript" src="fnc_adm.js"></script>

	<table height="100%">
<?makeMenuAdmin($page, $menu);?>
	<tr>
		<td width="100%">
			<table cellspacing="0">
			<tr>
<td style="padding-right: 30px;">
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_gb') !== false) { ?><input type="radio" class="inp_chk" value="gb" id="view_gb" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'gb')?' checked':'';?>><label for="view_gb" class="txt_bld txt_look">GB</label><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_mail') !== false) { ?><input type="radio" class="inp_chk" value="mail" id="view_mail" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'mail')?' checked':'';?>><label for="view_mail" class="txt_bld txt_look">MAIL</label><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_pimg') !== false) { ?><input type="radio" class="inp_chk" value="img" id="view_img" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'img')?' checked':'';?>><label for="view_img" class="txt_bld txt_look">PROFILBILD</label><br><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_chat') !== false) { ?><input type="radio" class="inp_chk" value="chat" id="view_chat" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'chat')?' checked':'';?>><label for="view_chat" class="txt_bld txt_look">CHAT</label><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_pho') !== false) { ?><input type="radio" class="inp_chk" value="photo" id="view_photo" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'photo')?' checked':'';?>><label for="view_photo" class="txt_bld txt_look">FOTOALBUM</label><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_blog') !== false) { ?><input type="radio" class="inp_chk" value="blog" id="view_blog" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'blog')?' checked':'';?>><label for="view_blog" class="txt_bld txt_look">BLOGG</label><? } ?>
</td>
<td style="padding-right: 30px;">
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_tho') !== false) { ?><input type="radio" class="inp_chk" value="thought" id="view_thought" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'thought')?' checked':'';?>><label for="view_thought" class="txt_bld txt_look">TYCK TILL</label><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_scc') !== false) { ?><input type="radio" class="inp_chk" value="scc" id="view_scc" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'scc')?' checked':'';?>><label for="view_scc" class="txt_bld txt_look">VISDOM</label><? } ?>
			<? if($isCrew || strpos($_SESSION['u_a'][1], 'obj_abuse') !== false) { ?><input type="radio" class="inp_chk" value="abuse" id="view_abuse" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'abuse')?' checked':'';?>><label for="view_abuse" class="txt_bld txt_look">ABUSE</label><? } ?>
</td>
<td>
			<? /* if($isCrew || strpos($_SESSION['u_a'][1], 'obj_ue') !== false) { ?><input type="radio" class="inp_chk" value="ue" id="view_ue" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'ue')?' checked':'';?>><label for="view_ue" class="txt_bld txt_look">BILDMAIL</label><? } */ ?>
			<? /* if($isCrew || strpos($_SESSION['u_a'][1], 'obj_event') !== false) { ?><input type="radio" class="inp_chk" value="event" id="view_event" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'event')?' checked':'';?>><label for="view_event" class="txt_bld txt_look">EVENT</label><? } */ ?>
			<? /* if($isCrew || strpos($_SESSION['u_a'][1], 'obj_sms') !== false) { ?><input type="radio" class="inp_chk" value="sms" id="view_sms" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'sms')?' checked':'';?>><label for="view_sms" class="txt_bld txt_look">SMS</label><br><? } */ ?>
			<? /* if($isCrew || strpos($_SESSION['u_a'][1], 'obj_full') !== false) { ?><input type="radio" class="inp_chk" value="full" id="view_full" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'full')?' checked':'';?>><label for="view_full" class="txt_bld txt_look">HÖGUPPLÖST</label><? } */ ?>
			<? /* if($isCrew || strpos($_SESSION['u_a'][1], 'obj_tele') !== false) { ?><input type="radio" class="inp_chk" value="tele" id="view_tele" onclick="document.location.href = 'obj.php?status=' + this.value;"<?=($status == 'tele')?' checked':'';?>><label for="view_tele" class="txt_bld txt_look">TELEFON</label><? } */ ?>
</td>
</tr>
</table>

<hr /><div class="hr"></div>
<br>
