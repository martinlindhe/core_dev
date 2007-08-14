			<tr><td>
			<table width="100%">
			<tr>
				<td>
Läste nyhetsbrevet <a href="send.php?t&show=<?=$r['main_id']?>"><?=secureOUT('VECKA '.$r['n_week'].' - '.$r['n_cmt'])?></a> med e-postadressen: <b class="txt_chead"><?=secureOUT($row['unique_id'])?></b> <?=niceDate($row['date_cnt'])?>.
				</td>
			</tr>
			<tr>
				<td style="padding: 8px 0 0 0;"><a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($row['sess_id'])?>"><?=substr(secureOUT($row['sess_id']), 0, 5)?></a> | <a href="gb.php?all=<?=$view_gb?>&s=<?=secureOUT($row['sess_ip'])?>"><?=secureOUT($row['sess_ip'])?></a></td>
			</tr>
			<tr><td style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>