			<tr><td>
			<table width="100%">
			<tr> 
				<td colspan="2" style="padding-bottom: 8px;"><img src="./_img/status_<?=($r['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:<?=$r['main_id']?>:2" onclick="changeStatus2('status', this.id);"><img src="./_img/status_<?=($r['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:<?=$r['main_id']?>:2" onclick="changeStatus2('status', this.id);"><?=(is_md5($r['logged_in']))?'<a href="user.php?t&id='.secureOUT($r['logged_in']).'"><span class="txt_big">'.secureOUT($r['gb_name']).'</span></a>':'<span class="txt_bld txt_big">'.secureOUT($r['gb_name']).'</span>';?> - <em>inlägg skrivet <?=niceDate($r['gb_date'])?></em> (#<?=$r['main_id']?>)</td>
			</tr>
			<tr>
				<td colspan="2">
<?=doURL(secureOUT($r['gb_msg']))?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="txt_other">
<?	if(!empty($r['gb_answer'])) { ?>
<br><?=$r['gb_answer']?><br><br><span class="txt_bld"><?=$r['user_name']?></span> - <em>inlägg besvarat <?=niceDate($r['gb_answerdate'])?></em>
<?	} else { ?>
<br><b>OBESVARAT</b>
<?	} ?>
				</td>
			</tr>
			<tr>
				<td style="padding: 8px 0 0 0;"><a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($r['sess_id'])?>"><?=substr(secureOUT($r['sess_id']), 0, 5)?></a> | <a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($r['gb_ip'])?>"><?=secureOUT($r['gb_ip'])?></a></td>
				<td align="right" height="10" style="padding: 8px 0 0 0;">
<input type="hidden" name="status_id:<?=$r['main_id']?>:2" id="status_id:<?=$r['main_id']?>" value="<?=$r['status_id']?>">
<a href="gb.php?del=<?=$r['main_id']?>" onclick="return confirm('Säker ?');">RADERA</a>
|
<a href="gb.php?id=<?=$r['main_id']?>">ÄNDRA</a>
|
<a href="javascript:openWin('gb_answer.php?id=<?=$r['main_id']?>');">SVARA</a> 
				</td>
			</tr>
			<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>