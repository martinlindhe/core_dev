<!-- design_foot.php start -->
</div> <!-- body_holder -->
<?
	if ($config['debug'] && $_SESSION['isAdmin']) {
		//admin menu

		echo '<div id="footer">';
		
		echo 'user: <b>'.$_SESSION['userName'].'</b> ';

		global $time_start;
		$time = number_format(microtime(true) - $time_start, 3);

		if ($_SESSION['isSuperAdmin']) {
			echo $time.'s, <a href="#" onClick="return toggle_div(\'db_layer\');">'.$db['queries'].' sql</a>';
		} else {
			echo $time.'s, '.$db['queries'].' sql';
		}
		echo ' ['.date('H:i').']';

		//Shows all SQL queries from this page view
		if ($_SESSION['isSuperAdmin']) {
			$sql_height = $db['queries']*32;
			if ($sql_height > 200) $sql_height = 200;
					
			echo '<div id="db_layer" class="db_layer" style="height:'.$sql_height.'px; display: none;">';
			for ($i=1; $i<=$db['queries']; $i++) {
				echo htmlentities(nl2br($db['query'][$i]), ENT_COMPAT, 'UTF-8')."\n";
				if ($i < $db['queries']) echo '<hr>';
			}
			echo '</div>';
		}
		echo '</div>';
	}
?>
</body></html>