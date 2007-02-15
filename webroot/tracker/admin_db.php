<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
/*	
	if (isset($_GET['empty_infofieldhistory'])) {
		clearInfoFieldHistory($db);
		JS_Alert('All infofield revision history has been cleared!');
	}
*/

	if (isset($_GET['reset_qcache'])) {
		//You need the RELOAD privilege for this operation
		dbQuery($db, 'RESET QUERY CACHE');
		JS_Alert('MySQL query cache has been reset!');
	}

	include('design_head.php');

		$content = '';
	
		/* Show MySQL query cache settings */
		$data = dbArrayNamedFields($db, 'SHOW VARIABLES LIKE "%query_cache%"', 'Variable_name', 'Value');
		if ($data['have_query_cache'] == 'YES') {
			$content .= '<b>MySQL query cache settings:</b><br>';
			$content .= 'Type: '. $data['query_cache_type'].'<br>';		//valid values: ON, OFF or DEMAND
			$content .= 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br>';
			$content .= 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br>';
			$content .= 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br>';
			$content .= 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br><br>';
	
			/* Current query cache status */
			$data = dbArrayNamedFields($db, 'SHOW STATUS LIKE "%Qcache%"', 'Variable_name', 'Value');
			$content .= '<b>MySQL query cache status:</b><br>';
			$content .= 'Hits: '. formatNumber($data['Qcache_hits']).'<br>';
			$content .= 'Inserts: '. formatNumber($data['Qcache_inserts']).'<br>';
			$content .= 'Queries in cache: '. formatNumber($data['Qcache_queries_in_cache']).'<br>';
			$content .= 'Total blocks: '. formatNumber($data['Qcache_total_blocks']).'<br>';
			$content .= '<br>';
			$content .= 'Not cached: '. formatNumber($data['Qcache_not_cached']).'<br>';
			$content .= 'Free memory: '. formatDataSize($data['Qcache_free_memory']).'<br>';
			$content .= '<br>';
			$content .= 'Free blocks: '. formatNumber($data['Qcache_free_blocks']).'<br>';
			$content .= 'Lowmem prunes: '. formatNumber($data['Qcache_lowmem_prunes']).'<br><br>';
	
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?reset_qcache">Reset query cache</a><br><br>';
		} else {
			$content .= '<b>MySQL Qcache is disabled!</b><br><br>';
		}

		/*
		//commented out because: disk_free_space, disk_total_space returns warning with php 5.2-dev on invalid drives
		$content .= 'Disk space report:';
		$content .= '<table cellpadding=0 cellspacing=0 border=1>';
		$content .= '<tr><th>Disk drive</th><th>Free space</th><th>Total space</th></tr>';
		for ($i='C'; $i<'Z'; $i++) {
			$drive = $i.':';
	
			$curr_free = disk_free_space($drive);
			$curr_tot = disk_total_space($drive);
			if ($curr_tot) {
				$proc_free = round(($curr_free / $curr_tot)*100, 1);
				$content .= '<tr><td>'.$drive.'</td><td>'.formatDataSize($curr_free).' ('.$proc_free.'%)</td><td>'.formatDataSize($curr_tot).'</td></tr>';
			}
		}
		$content .= '</table><br>';*/

		if ($config['phpmyadmin']) {
			$content .= '<a href="'.$config['phpmyadmin'].'" target="_blank">phpmyadmin</a><br><br>';
		}
		//$content .= '<a href="'.$_SERVER['PHP_SELF'].'?empty_infofieldhistory">empty all infofield revision history</a> ('.getInfoFieldHistoryCountAll($db).' entries)<br><br>';
		//$content .= '<a href="'.$_SERVER['PHP_SELF'].'?empty_thumbnailcache">empty thumbnail cache</a><br><br>';
		
		echo 'Databasen<br><br>'. $content;

	include('design_foot.php');
?>