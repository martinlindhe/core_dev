<?
	$faq = $sql->query("SELECT * FROM {$t}faq WHERE item_type = 'F' ORDER BY order_id", 0, 1);

	require(DESIGN.'head.php');
?>

<img src="/_gfx/ttl_faq.png" alt="Hjälp &amp; FAQ"/><br/><br/>

<div id="contentSmall" style="padding-left: 10px;">
	<div class="box2">
		<div class="box2mid" style="padding-top: 38px;">
			<table summary="" width="360" style="margin-bottom: 12px;">
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
		<div class="boxMid2mid" style="padding-top: 38px;">
			<table summary="" cellspacing="0" width="500">
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
	require(DESIGN.'foot.php');
?>