<?
	$faq = $sql->query("SELECT ".CH." * FROM {$t}faq WHERE item_type = 'F' ORDER BY order_id", 0, 1);

	require(DESIGN.'head.php');
?>
<script type="text/javascript">
var oldsel = 0;
function selectFAQ(sel) {
	if(oldsel) {
		document.getElementById('F' + oldsel).className = '';
	}
	document.getElementById('F' + sel).className = 'wht';
	oldsel = sel;
}
</script>
		<div id="contentSmall" style="padding-left: 10px;">
<div class="box2">
	<img src="<?=CS?>_objects/_heads/head_faq.png" style="position: absolute; top: -10px; left: -10px;" />
	<div class="box2mid" style="padding-top: 38px;">
		<table width="360" style="margin-bottom: 12px;">
<?
	if(count($faq) && !empty($faq)) {
		foreach($faq as $row) {
		echo '<tr><td class="bld" style="padding-top: 5px;"><a href="#F'.$row['main_id'].'" onclick="selectFAQ(\''.$row['main_id'].'\');"> - '.extOUT($row['item_q']).'</a></td></tr>';
		}
	} else echo '<tr><td class="spac_b pdg cnt">Inga inlägg finns.</td></tr>';
?>
		</table>
	</div>
</div>
		</div>
		<div id="contentBig">
<div class="boxMid2">
	<img src="<?=CS?>_objects/_heads/head_faq_q.png" style="position: absolute; top: -10px; left: -10px;" />
	<div class="boxMid2mid" style="padding-top: 38px;">
		<table cellspacing="0" width="500">
<?
	if(count($faq) && !empty($faq)) {
		foreach($faq as $row) {
		echo '<tr><td class="pdg_nt bld up" style="padding-top: 10px; width: 20px;">Q</td><td class="pdg_nt bld up" style="padding-top: 10px;"><a name="F'.$row['main_id'].'" id="F'.$row['main_id'].'">'.extOUT($row['item_q']).'</a></td></tr>';
		echo '<tr><td class="pdg spac_b" style="width: 20px;">A</td><td class="pdg spac_b" style="padding-bottom: 12px;">'.extOUT($row['item_a']).'</td></tr>';
		}
	}
?>
		</table>
	</div>
</div>
		</div>
<br class="clr" />
<?
	#require(DESIGN.'foot_info.php');
	require(DESIGN.'foot.php');
?>