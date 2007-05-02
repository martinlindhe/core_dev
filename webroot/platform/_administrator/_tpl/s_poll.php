			<tr><td>
			<table width="100%">
			<tr>
				<td colspan="2">
<?=(!$r['is_enabled'])?'<b>SUBTRAHERAD</b> ':'';?>Svarade <b class="txt_chead"><?=$r['poll_ans'.$r['unique_id']]?></b> på frågan: <a href="poll.php?t&view=<?=$r['category_id']?>&answer=<?=$r['unique_id']?>"><?=$r['poll_quest']?></a> <?=niceDate($r['date_cnt'])?>.
				</td>
			</tr>
			<tr>
				<td style="padding: 8px 0 0 0;"><a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($r['sess_id'])?>"><?=substr(secureOUT($r['sess_id']), 0, 5)?></a> | <a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($r['sess_ip'])?>"><?=secureOUT($r['sess_ip'])?></a></td>
				<td align="right" height="10" style="padding: 8px 0 0 0;">
<a href="poll.php?t&view=<?=$r['category_id']?>&answer=<?=$r['unique_id']?>&<?=($r['is_enabled'])?'deny='.$r['main_id'].'">SUBTRAHERA':'acc='.$r['main_id'].'">ADDERA';?></a>
				</td>
			</tr>
			<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>