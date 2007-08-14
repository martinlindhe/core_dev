			<tr><td>
			<table width="100%">
			<tr>
				<td colspan="2">
Anmälde <b class="txt_chead"><?=secureOUT($r['ins_fname'].' '.$r['ins_sname'])?></b> till nyhetsbrevet <?=niceDate($r['date_cnt'])?>.
				</td>
			</tr>
			<tr>
				<td style="padding: 8px 0 0 0;"><a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($row['sess_id'])?>"><?=substr(secureOUT($row['sess_id']), 0, 5)?></a> | <a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($row['sess_ip'])?>"><?=secureOUT($row['sess_ip'])?></a></td>
				<td align="right" height="10" style="padding: 8px 0 0 0;">
ID: #<?=$r['main_id']?>
				</td>
			</tr>
			<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>