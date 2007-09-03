			<tr><td>
			<table width="100%">
			<tr>
				<td colspan="2"<?=($r['picstatus'] == '2' || $r['status_id'] == '2')?' class="bg_blk"':'';?>>
				<table width="100%">
				<tr>
					<td rowspan="2"><a href="javascript:vimmel('<?=$r['picmain_id']?>', 692, 625);"><img src="<?=$pic?>" style="margin-right: 5px;"></a></td> <!-- onmouseover="showBig(this);" onmouseout="showBig(this);" -->
					<td style="padding-bottom: 8px; height: 10px;"<?=($r['picstatus'] == '2' || $r['status_id'] == '2')?' class="txt_wht"':'';?>><img src="./_img/status_<?=($r['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:<?=$r['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($r['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:<?=$r['main_id']?>" onclick="changeStatus('status', this.id);"><?=(!empty($r['c_email']))?((is_md5($r['c_email']))?'<a style="text-decoration: underline;" href="user.php?t&id='.secureOUT($r['c_email']).'"><span class="txt_big">'.secureOUT($r['c_name']).'</span></a>':'<a href="mailto:'.secureOUT($r['c_email']).'"><span class="txt_big">'.secureOUT($r['c_name']).'</span></a>'):'<span class="txt_bld txt_big">'.secureOUT($r['c_name']).'</span>';?> - <em>inl&auml;gg skrivet <?=niceDate($r['c_date'])?></em> (#<?=$r['main_id']?>)</span></td>
				</tr>
				<tr> 
					<td style="padding: 0; height: 55px;"<?=($r['picstatus'] == '2' || $r['status_id'] == '2')?' class="txt_wht"':'';?>>
<?=doURL(secureOUT($r['c_msg']))?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td<?=($r['picstatus'] == '2' || $r['status_id'] == '2')?' class="bg_blk"':'';?> style="padding: 8px 0 0 0;"><a href="gb.php?<?=(!$extern)?'t':'';?>&s=<?=secureOUT($r['sess_id'])?>"><?=substr(secureOUT($r['sess_id']), 0, 5)?></a> | <a href="gb.php?<?=(!$extern)?'t':'';?>&s=<?=secureOUT($r['sess_ip'])?>"><?=secureOUT($r['sess_ip'])?></a></td>
				<td align="right" style="height: 12px; padding: 8px 0 0 0;"<?=($r['picstatus'] == '2' || $r['status_id'] == '2')?' class="bg_blk"':'';?>>
<input type="hidden" name="pic_id:<?=$r['main_id']?>" value="<?=$r['picmain_id']?>">
<input type="hidden" name="str_id:<?=$r['main_id']?>" value="<?=$r['pic_id']?>">
<input type="hidden" name="picstatus:<?=$r['main_id']?>" value="<?=$r['picstatus']?>">
<input type="hidden" name="logged_id:<?=$r['main_id']?>" value="<?=$r['c_email']?>">
<input type="hidden" name="otatus_id:<?=$r['main_id']?>" value="<?=$r['status_id']?>">
<input type="hidden" name="status_id:<?=$r['main_id']?>" id="status_id:<?=$r['main_id']?>" value="<?=$r['status_id']?>">
<input type="hidden" name="topic_id:<?=$r['main_id']?>" value="<?=$r['topic_id']?>">
<a href="pics.php?<?=($extern)?'t&':'';?>view_cmt=<?=($extern)?$r['status_id']:$view_cmt;?>&del=<?=$r['main_id']?>&status=<?=$r['status_id']?>&picstatus=<?=$r['picstatus']?>" onclick="return confirm('Säker ?');">RADERA</a>
				</td>
			</tr>
			<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>