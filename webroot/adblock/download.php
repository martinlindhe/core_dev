<?
	require_once('config.php');

	define('CACHE_AGE', 3600*1);		//time before disk cache expires

	define('DOWNLOAD_METHOD_WEBFORM', 'webform');
	define('DOWNLOAD_METHOD_SUBSCRIPTION', 'subscription');
	define('DOWNLOAD_METHOD_RSS', 'rss');		//todo...
	
	$requestType = 0;
	
	if (isset($_POST['type_0']) || isset($_POST['type_1']) || isset($_POST['type_2']) || isset($_POST['type_3'])) {
		@$types = $_POST['type_0'].','.$_POST['type_1'].','.$_POST['type_2'].','.$_POST['type_3'];
		if ($types == ',,,') die;	//javascript blocks this from happening too
		$requestType = DOWNLOAD_METHOD_WEBFORM;
	}

	if (isset($_GET['type'])) {
		switch ($_GET['type']) {
			case 'unsorted':	$types = '0'; break;
			case 'ads':				$types = '1'; break;
			case 'trackers':	$types = '2'; break;
			case 'counters':	$types = '3'; break;
			case 'all':				$types = '0,1,2,3'; break;
			default: die;
		}
		$requestType = DOWNLOAD_METHOD_SUBSCRIPTION;
	}

	if ($requestType) {

		$type_ext = '';

		switch ($types) {
			case '0': case '0,,,':	$type_ext = '-unsorted'; break;
			case '1': case ',1,,':	$type_ext = '-ads'; break;
			case '2': case ',,2,':	$type_ext = '-trackers'; break;
			case '3': case ',,,3':	$type_ext = '-counters'; break;
			case '0,1,2,3';					$type_ext = '-all'; break;
			default:								$type_ext = '-custom-'.$types; break;
		}
	
		$datestr	= date('Ymd');
		$hour			= date('H');
		
		$cache_file = $config['adblock']['cachepath'].'adblockfilters'.$type_ext.'.txt';

		if ($db->debug) {
			$str = 'Downloaded ruleset '.$cache_file.' ('.$requestType.')';
			if (!empty($_GET['version'])) $str .= ' ('.strip_tags($_GET['version']).')';
			$db->log($str);
		}

		$lastchanged = 0;
		if (file_exists($cache_file)) {
			$lastchanged = filemtime($cache_file);
		}

		if ($lastchanged < time()-(CACHE_AGE))
		{
			$list = getAdblockRules($types);

			$fp = fopen($cache_file, 'w');
			fputs($fp, "[Adblock]\n");
			for ($i=0; $i<count($list); $i++) {
				fputs($fp, $list[$i]['ruleText']."\n");
			}
			fclose($fp);
		}
		
		if (DOWNLOAD_METHOD_SUBSCRIPTION) {
			/* Send special headers to the subscriber */
			header('Filterset-timestamp: '. $lastchanged);
		}

		sendTextFile($cache_file, basename($cache_file));
		die;
	}

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