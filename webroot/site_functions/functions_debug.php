<?
	function debugFooter($pageload_start)
	{
		global $db;

		$total_time = round(microtime(true) - $pageload_start, 3);

		if ($_SESSION['isSuperAdmin'])
		{
			//admin info (sql query time)
			echo $total_time.'s, <a href="#" onclick="return toggle_element_by_name(\'debug_layer\');">'.$db['queries'].' sql</a>';

			//Shows all SQL queries from this page view
			$sql_height = $db['queries']*30;
			if ($sql_height > 160) $sql_height = 160;
			
			$sql_time = 0;

			echo '<div id="debug_layer" style="height:'.$sql_height.'px; display: none; font-family: verdana; font-size: 9px;">';
			for ($i=1; $i<=$db['queries']; $i++)
			{
				$sql_time += $db['time_spent'][$i];
				
				echo '<div style="width: 50px; float: left;">';
					echo round($db['time_spent'][$i],3).'s';
					if (!empty($db['error'][$i])) echo '<img src="design/delete.png" title="'.$db['error'][$i].'">';
				echo '</div> ';

				echo htmlentities(nl2br($db['query'][$i]), ENT_COMPAT, 'UTF-8');
				if ($i < $db['queries']) echo '<hr/>';
			}

			echo '<hr/>';
			$php_time = $total_time - $sql_time;
			echo 'Total time spent - SQL: '.round($sql_time,3).'s, PHP: '.round($php_time,3).'s';
			echo '</div>';

		} else {
			echo $total_time.'s, '.$db['queries'].' sql';
		}
	}

	//used to write to logfile, for database errors, else we use logEntry() to store important events in database
	function debugLog($str)
	{
		global $config;

		if ($config['debug']) error_log('debugLog(): '.$str);
	}

?>