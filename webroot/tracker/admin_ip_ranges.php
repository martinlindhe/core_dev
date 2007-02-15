<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (empty($_GET['ci']) || !is_numeric($_GET['ci'])) die;
	
	$countryId = $_GET['ci'];
	
	include('design_head.php');
	
	echo '<h2>IP ranges</h2>';

	echo 'Displaying IP ranges from '.GeoIP_ci_to_Country($countryId).' '.showGeoIPCountryFlag($countryId).'<br><br>';
	
	echo '<a href="admin_ip_ranges_overview.php">Back to overview</a><br><br>';
	

	$list = getAllWHOISCacheEntries($countryId);

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th>IP Range</th>';
	//echo '<th>Space</th>';
	echo '<th>Population</th>';
	echo '<th>Owner name</th>';
	//echo '<th>Source</th>';
	echo '</tr>';

	$priv_cnt = 0;
	for ($i=0; $i<count($list); $i++)
	{
		if ($list[$i]['privateRange']) {
			$priv_cnt++;
			echo '<tr class="tr_grayed">';
		}	else {
			echo '<tr>';
		}

		echo '<td>';
			//echo getGeoIPCountryFlag($list[$i]['geoIP_start']).' ';
			echo GeoIP_to_IPv4($list[$i]['geoIP_start']).' - '.GeoIP_to_IPv4($list[$i]['geoIP_end']);
		echo '</td>';
		
		$space = ($list[$i]['geoIP_end'] - $list[$i]['geoIP_start'] + 1);
		//echo '<td>'.$space.'</td>';

		echo '<td align="right">';
			$cnt = getUniqueIPCountFromRange($db, $list[$i]['geoIP_start'], $list[$i]['geoIP_end']);
			echo $cnt;
			$cnt_pct = round($cnt / $space * 100, 2);
			echo ' ('.$cnt_pct.'%)';
			
		echo '</td>';

		echo '<td>';
			$name = $list[$i]['name'];
			if ($name) {
				//Cut down long names
				if (mb_strlen($name) > 40) $name = mb_substr($name, 0, 40).'<img src="design/scissors.png" width=12 height=16 align="top">';
			} else {
				$name = '<span class="objectCritical">No name</span>';
			}
			echo '<a href="#" onClick="return anon_popup(\'admin_popup_ip_owner.php?start='.$list[$i]['geoIP_start'].'&amp;end='.$list[$i]['geoIP_end'].'\')">'.$name.'</a>';
		echo '</td>';
		
		//echo '<td>'.$list[$i]['source'].'</td>';

		echo '</tr>';
	}
	echo '</table>';

	$priv_pct = round(($priv_cnt / count($list))*100, 2);
	echo count($list).' entries. '.$priv_cnt.' is marked PRIVATE ('.$priv_pct.'%)';
		


	include('design_foot.php');
?>