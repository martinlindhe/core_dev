<?
	require_once('config.php');

	//this function may end the script execution if user is served with a download
	handleAdblockDownloadRequest();

	require('design_head.php');

	$rules = getAdblockAllRulesCount();

	wiki('Download');

?>
<script type="text/javascript">
function checkDLform() {
	if (!document.dlruleset.type_0.checked && !document.dlruleset.type_1.checked && !document.dlruleset.type_2.checked && !document.dlruleset.type_3.checked) { alert('Please choose a category'); return false; }
	return true;
}
</script>
<br/>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?send" name="dlruleset">
<table width="500" cellpadding="0" cellspacing="0" border="0">
	<tr><td width="20">&nbsp;</td>
		<td class="centermenu">
			<input type="checkbox" name="type_1" id="type_1" value="1" checked="checked"/>
			<label for="type_1">Ads (<?=$rules['ads']?> entries)</label><br/>
			<input type="checkbox" name="type_2" id="type_2" value="2" checked="checked"/>
			<label for="type_2">Trackers (<?=$rules['trackers']?> entries)</label><br/>
			<input type="checkbox" name="type_3" id="type_3" value="3" checked="checked"/>
			<label for="type_3">Counters (<?=$rules['counters']?> entries)</label><br/>
			<input type="checkbox" name="type_0" id="type_0" value="0" checked="checked"/>
			<label for="type_0">Unsorted entries (<?=$rules['unsorted']?> entries)</label><br/><br/>
			<?=$rules["total"]?> entries total<br/><br/>
			<input type="submit" class="button" value="Download" onclick="return checkDLform();"/>
		</td>
	</tr>
</table>
</form>
<?
	require('design_foot.php');
?>