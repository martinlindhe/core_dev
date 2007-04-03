<?
	if($own) popupACT('Du kan inte skicka till dig själv.');
	$user->blocked($s['id_id']);
	if(!$isFriends) {
		$onlL = $user->getinfo($l['id_id'], 'private_chat');
		$onlS = $user->getinfo($s['id_id'], 'private_chat');
		if($onlL) { $closed = true; $n = 'Otillgänglig'; } elseif($onlS) { $closed = true; $n = 'Otillgänglig'; }
	}
	$NAME_TITLE = secureOUT($s['u_alias']).' | privatchat';
	require(DESIGN.'head_popup.php');
?>
<script type="text/javascript">
<?
if(!$closed)
echo '
var id = \''.$s['id_id'].'\';
var usr = \''.($s['u_alias']).'\';
var you = \''.($l['u_alias']).'\';
';
else
echo '
var id = \''.$id.'\';
var usr = \''.($n.' användare').'\';
var you = \''.($l['u_alias']).'\';
';
?>
</script>
<script type="text/javascript" src="<?=OBJ?>xml_chat.js"></script>
<script type="text/javascript">document.onkeydown = ActivateByKey;</script>
<div class="boxMid1" style="margin: 15px 15px 0 15px;">
		<img src="<?=CS?>_objects/_heads/head_chat.png" style="position: absolute; top: -10px; left: -10px;" />
	<div class="boxMid1mid">
<table cellspacing="0" style="width: 510px; height: 100%;">
<tr>
	<td class="rgt" style="padding-bottom: 6px;"><?=(!$closed)?$user->getstring($s, '', array('nolink'=>1)):$n.' användare';?></td>
</tr>
<tr>
	<td height="100%">
		<table cellspacing="0" width="510" style="height: 100%;" class="lft">
		<tr>
			<td style="padding: 6px 6px 0 6px;"><iframe name="msgs" src="<?=l('user', 'chatwin', $s['id_id'])?>" allowtransparency="true" frameborder="no" scrolling="auto" style="width:270px; height:300px;"></iframe></td>
			<td style="width: 156px; height: 306px; padding-top: 6px;"><?=(!$closed)?$user->getimg($s['id_id'].$s['u_picid'].$s['u_picd'].$s['u_sex'], 1, array('toparent' => 1)):'';?></td>
		</tr>
		<tr>
			<td colspan="2" class="btm" style="padding-top: 6px; padding-left: 5px;"><b>Meddelande:</b>&nbsp;&nbsp;&nbsp;(<span id="cha_lim">250</span> tecken kvar)<br /><textarea name="msgTextbox"<?=($closed)?' readonly':'';?> class="txt" id="msgTextbox" onblur="gotFocus = false;" onclick="taskStop();" onfocus="taskStop();" style="height: 50px; width: 390px;" onkeyup="fixlimit(this, 'cha_lim');" onkeydown="fixlimit(this, 'cha_lim');" onchange="fixlimit(this, 'cha_lim');"></textarea></td>
		</tr>
		</table>
	</td>
</tr>
</table>
	</div>
	<img onclick="<?=(!$closed?' javascript:doMSG();':'')?>" accesskey="s" style="cursor: pointer; position: absolute; margin: 0; padding: 0; right: -10px; bottom: -11px;" src="/_objects/_heads/btn1_send.png" /></a>
</div>
</body>
</html>