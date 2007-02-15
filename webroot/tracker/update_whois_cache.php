<?
	//Script to be called regularry, to update all expired WHOIS cache entries
	
	include_once('include_all.php');

	$time_start = time();
	echo '<pre>';
	echo 'WHOIS Cache update started at '.getDateStringShort($time_start).".\n\n";

	$list = getAllWHOISCacheEntries();
	
	$update_cnt = 0;

	for ($i=0; $i<count($list); $i++) {
		if ($list[$i]['timeUpdated'] < time()-$config['whois']['cache_expire']) {
			$check = forceWHOISCacheUpdate($list[$i]['geoIP_start']);
			
			if ($check) {
				echo 'Updated WHOIS cache for '.GeoIP_to_IPv4($list[$i]['geoIP_start']).' - '.GeoIP_to_IPv4($list[$i]['geoIP_end'])."\n";
			} else {
				echo '<b>ERROR: Update FAILED for WHOIS cache '.GeoIP_to_IPv4($list[$i]['geoIP_start']).' - '.GeoIP_to_IPv4($list[$i]['geoIP_end'])."</b>\n";
			}
			$update_cnt++;
		}
	}
	
	$time_end = time();
	echo "\n";
	echo 'WHOIS Cache update completed at '.getDateStringShort($time_end).' ';
	echo '('.makeTimePeriodShort($time_end-$time_start).").\n";

	$update_pct = round($update_cnt / count($list) * 100, 1);
	echo $update_cnt.' entries out of '.count($list).' ('.$update_pct.'%) were updated.';

?>