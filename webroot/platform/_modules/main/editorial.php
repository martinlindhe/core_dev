<?
	$view = false;
	$list = $sql->query("SELECT ".CH." main_id, ad_title FROM {$t}editorial WHERE status_id = '1' ORDER BY ad_date DESC", 0, 1);
	if(!empty($id)) {
		$inf = $sql->queryLine("SELECT ".CH." ad_title, ad_cmt, ad_date FROM {$t}editorial WHERE status_id = '1' AND main_id = '".secureINS($id)."' LIMIT 1", 1);
		if(!empty($inf) && count($inf)) {
			$view = true;
		}
	}
	if(!$view) {
		$inf = $sql->queryLine("SELECT ".CH." ad_title, ad_cmt, ad_date FROM {$t}editorial WHERE status_id = '1' ORDER BY ad_date DESC LIMIT 1", 1);
	}
	require(DESIGN.'head.php');
?>
		<div id="contentSmall" style="padding-left: 10px;">
<div class="box4">
	<img src="<?=CS?>_objects/_heads/head_editorial_last.png" style="position: absolute; top: -10px; left: -10px;" />
	<div class="box4mid" style="padding-top: 38px;">
		<table width="360" style="margin-bottom: 12px;">
<?
	if(count($list) && !empty($list)) {
		foreach($list as $row) {
		echo '<tr><td class="bld" style="padding-top: 5px;"><a href="'.l('main', 'editorial', $row['main_id']).'"> - '.extOUT($row['ad_title']).'</a></td></tr>';
		}
	} else echo '<tr><td class="spac_b pdg cnt">Inga inlägg finns.</td></tr>';
?>
		</table>
	</div>
</div>
		</div>
		<div id="contentBig">
<div class="boxMid4">
	<img src="<?=CS?>_objects/_heads/head_editorial.png" style="position: absolute; top: -10px; left: -10px;" />
	<div class="boxMid4mid" style="padding-top: 38px;">
		<table cellspacing="0" width="500">
<?
	if(!empty($inf) && count($inf)) {
echo '<h3>'.extOUT($inf['ad_title']).'</h3>'.extOUT($inf['ad_cmt']);
	}
?>
<div class="v em"><br />Publicerad: <?=nicedate($inf['ad_date'])?></div>
		</table>
	</div>
</div>
		</div>
<br class="clr" />
<?
	require(DESIGN.'foot.php');
?>