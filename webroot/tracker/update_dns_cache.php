<?
	//Script to be called regularry, to update all expired DNS cache entries
	
	include_once('include_all.php');

	$time_start = time();
	echo 'DNS Cache update started at '.getDateStringShort($time_start).".\n\n";

	$list = getAllDNSCacheEntries();
	
	$update_cnt = 0;

	for ($i=0; $i<count($list); $i++) {
		if ($list[$i]['timeCreated'] < time()-$config['dns_cache_expiration']) {
			$host = updateDNSCache($list[$i]['IP']);
			echo 'Updated DNS cache for '.GeoIP_to_IPv4($list[$i]['IP']).' to '.$host."\n";
			$update_cnt++;
		}
	}
	
	$time_end = time();
	echo "\n";
	echo 'DNS Cache update completed at '.getDateStringShort($time_end).' ';
	echo '('.makeTimePeriodShort($time_end-$time_start).").\n";
	
	$update_pct = round($update_cnt / count($list) * 100, 1);
	echo $update_cnt.' entries out of '.count($list).' ('.$update_pct.'%) were updated.';

?>