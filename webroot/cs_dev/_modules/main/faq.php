<?
	$faq = $db->getArray('SELECT * FROM s_faq WHERE item_type = "F" ORDER BY order_id');

	require(DESIGN.'head.php');
?>

<div class="subHead">hjälp &amp; faq</div><br class="clr"/>

<div id="contentSmall" style="padding-left: 10px;">
	<div class="box2">
		<div class="box2mid">
			<?
				if(count($faq) && !empty($faq)) {
					foreach($faq as $row) {
						echo '<div style="padding-top: 5px;"><a href="#F'.$row['main_id'].'" onclick="selectFAQ(\''.$row['main_id'].'\');"> - '.extOUT($row['item_q']).'</a></div>';
					}
				} else echo 'Inga inlägg finns.';
			?>
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
					echo '<tr><td class="pdg_nt bld up" style="padding-top: 10px; width: 20px;"><a name="F'.$row['main_id'].'"></a>Q</td><td class="pdg_nt bld up" style="padding-top: 10px;">'.extOUT($row['item_q']).'</td></tr>';
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