<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_POST) && $_GET['option']) {
		//echo '<pre>'; print_r($_POST);

		/* Process form */
		$_SESSION['timedsubscription']['frequency'] = $_POST['frequency'];
		switch ($_SESSION['timedsubscription']['frequency']) {
			case 'daily':
				$_SESSION['timedsubscription']['hour1'] = $_POST['daily_hour1'];
				$_SESSION['timedsubscription']['minute1'] = $_POST['daily_minute1'];
				break;

			default:
				echo '<pre>';
				print_r($_POST);
		}

		$_SESSION['timedsubscription']['start_day'] = $_POST['start_day'];
		$_SESSION['timedsubscription']['start_month'] = $_POST['start_month'];
		$_SESSION['timedsubscription']['start_year'] = $_POST['start_year'];
		header('Location: timedsubscriptions_confirm.php?option='.$_GET['option']);
		die;
	}

	include('design_head.php');
?>
<script type="text/javascript">
function submitform(name){
	eval('box = document.forms[0].'+name);
	id = box.options[box.selectedIndex].value;
	if (id) window.location='<?=$_SERVER['PHP_SELF']?>?option='+id;
}
</script>
<?
	echo '<b>Timed Subscriptions - Overview</b><br><br>';
	
	$option = 0;
	if (!empty($_GET['option']) && is_numeric($_GET['option'])) $option = $_GET['option'];

	if (empty($option)) {
		echo 'First, choose something to be reminded of:<br><br>';

		$list = getTimedSubscriptionCategories($db, 0); //root level categories
		echo '<form>';
		for ($i=0; $i<count($list); $i++) {
			echo '<b>'.($i+1).'</b>: '.$list[$i]['categoryName'].'<br>';

			$sublist = getTimedSubscriptionCategories($db, $list[$i]['categoryId']);
			echo '<select name="cat'.$list[$i]['categoryId'].'" onchange="submitform(\'cat'.$list[$i]['categoryId'].'\')">'. "\n";
			echo '<option>';
			for ($j=0; $j<count($sublist); $j++) {
				echo '<option value="'.$sublist[$j]['categoryId'].'">'.$sublist[$j]['categoryName'];
			}
			echo '</select>';
			echo '<br>';
		}
		echo '</form>';

	} else {
		
		$data = getTimedSubscriptionCategory($db, $option);
		
		echo '<span class="msg_success">You have selected '.$data['parentName'].' - '.$data['categoryName'].'</span><br><br>';
		echo 'Next, choose how often you want to be reminded<br><br>';
		echo

			'<script type="text/javascript">'.
			'function select_daily(){'.
				'show_div(\'layer_daily_picker\');'.
				'hide_div(\'layer_weekly_picker\');'.
				'hide_div(\'layer_monthly_picker\');'.
				'hide_div(\'layer_onetime_picker\');'.
				'show_div(\'layer_duration_n_start_picker\');'.
			'}'.
			'function select_weekly(){'.
				'hide_div(\'layer_daily_picker\');'.
				'show_div(\'layer_weekly_picker\');'.
				'hide_div(\'layer_monthly_picker\');'.
				'hide_div(\'layer_onetime_picker\');'.
				'show_div(\'layer_duration_n_start_picker\');'.
			'}'.
			'function select_monthly(){'.
				'hide_div(\'layer_daily_picker\');'.
				'hide_div(\'layer_weekly_picker\');'.
				'show_div(\'layer_monthly_picker\');'.
				'hide_div(\'layer_onetime_picker\');'.
				'show_div(\'layer_duration_n_start_picker\');'.
			'}'.

			'function select_onetime(){'.
				'hide_div(\'layer_daily_picker\');'.
				'hide_div(\'layer_weekly_picker\');'.
				'hide_div(\'layer_monthly_picker\');'.
				'show_div(\'layer_onetime_picker\');'.
				'hide_div(\'layer_duration_n_start_picker\');'.
			'}'.
			'</script>'.

			'<form method="post" action="'.$_SERVER['PHP_SELF'].'?option='.$option.'" name="choosetime">'.
					'<input type="radio" name="frequency" value="daily" onclick="select_daily()" checked>Dagligen '.
					'<input type="radio" name="frequency" value="weekly" onclick="select_weekly()">Veckovis '.
					'<input type="radio" name="frequency" value="monthly" onclick="select_monthly()">M&aring;nadsvis'.
					'<input type="radio" name="frequency" value="onetime" onclick="select_onetime()">Eng&aring;ngsp&aring;minnelse';

		echo '<br><br>';

		echo '<div id="layer_daily_picker" style="overflow:auto; width:100%;" class="msg_success">';
		echo 'Remind me at ';
		echo '<select name="daily_hour1">';
		for ($i=0; $i<24; $i++) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'"';
			if ($i == '08') echo ' selected>'.$i; else echo '>'.$i;
		}
		echo '</select> <b>:</b> ';

		echo '<select name="daily_minute1">';
		for ($i=0; $i<59; $i+=5) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'">'.$i;
		}
		echo '</select> every day';
		echo '<br><br>';
		echo '<input name="daily_2ndtime" type="checkbox" onclick="toggle_div(\'layer_daily2_picker\')"> Remind me a second time of the day about this event<br>';
			echo '<div id="layer_daily2_picker" style="display: none; overflow:auto; width:100%;">';

			echo '<br>';
			echo 'I want to be reminded again at ';
			echo '<select name="daily_hour2">';
			for ($i=0; $i<24; $i++) {
				if ($i<10) $i = '0'.$i;
				echo '<option value="'.$i.'"';
				if ($i == '16') echo ' selected>'.$i; else echo '>'.$i;
			}
			echo '</select> <b>:</b> ';

			echo '<select name="daily_minute2">';
			for ($i=0; $i<59; $i+=5) {
				if ($i<10) $i = '0'.$i;
				echo '<option value="'.$i.'">'.$i;
			}
			echo '</select>';
		
			echo '</div>';
		echo '</div>';

		echo '<div id="layer_weekly_picker" style="display: none; overflow:auto; width:100%;" class="msg_success">';
		echo 'Remind me at ';
		echo '<select name="weekly_hour1">';
		for ($i=0; $i<24; $i++) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'"';
			if ($i == '08') echo ' selected>'.$i; else echo '>'.$i;
		}
		echo '</select> <b>:</b> ';
		echo '<select name="weekly_minute1">';
		for ($i=0; $i<59; $i+=5) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'">'.$i;
		}
		echo '</select>';
		echo ' the following days each week<br><br>';
		echo '<input name="weekly_mon" type="checkbox" checked>Monday<br>';
		echo '<input name="weekly_tue" type="checkbox" checked>Tuesday<br>';
		echo '<input name="weekly_wed" type="checkbox" checked>Wednesday<br>';
		echo '<input name="weekly_thu" type="checkbox" checked>Thursday<br>';
		echo '<input name="weekly_fri" type="checkbox" checked>Friday<br>';
		echo '<input name="weekly_sat" type="checkbox">Saturday<br>';
		echo '<input name="weekly_sun" type="checkbox">Sunday<br>';
		echo '</div>';

		echo '<div id="layer_monthly_picker" style="display: none; overflow:auto; width:100%;" class="msg_success">';
		echo 'Remind me at ';

		echo '<select name="monthly_hour1">';
		for ($i=0; $i<24; $i++) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'"';
			if ($i == '08') echo ' selected>'.$i; else echo '>'.$i;
		}
		echo '</select> <b>:</b> ';

		echo '<select name="monthly_minute1">';
		for ($i=0; $i<59; $i+=5) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'">'.$i;
		}
		echo '</select>';
		
		echo ', the ';

		echo '<select name="monthly_day">';
		for ($i=1; $i<=31; $i++) echo '<option value="'.$i.'">'.$day['pron'][$i];
		//echo '<option>last';	//todo!
		echo '</select>';
		echo ' day of the following months each year<br>';
		echo '<br>';

		echo '<table cellpadding=0 cellspacing=0><tr><td width=200>';
		for ($i=1; $i<=12; $i++) {
			echo '<input name="monthly_month'.$i.'" type="checkbox" checked>'.$month['long'][$i].'<br>';
			if ($i==6) echo '</td><td width=200>';
		}
		echo '</td><td valign="bottom">';
		echo 'Yearly reminder: <a href="javascript:wnd_url(\'calendarpicker.php\',300,200)"><img src="icons/calendar.png" title="Pick a date..." width=16 height=16></a>';
		echo '</td></tr></table>';
		echo '</div>';

		echo '<div id="layer_onetime_picker" style="display: none; overflow:auto; width:100%;" class="msg_success">';
		echo 'Remind me at ';
		echo '<select name="onetime_hour1">';
		for ($i=0; $i<24; $i++) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'"';
			if (intval($i) == date('G')+1) echo ' selected>'.$i; else echo '>'.$i;
		}
		echo '</select> <b>:</b> ';

		echo '<select name="onetime_minute1">';
		for ($i=0; $i<59; $i+=5) {
			if ($i<10) $i = '0'.$i;
			echo '<option value="'.$i.'">'.$i;
		}
		echo '</select>';
		echo '<br><br>';
		echo 'The ';
		echo '<select name="onetime_day">';
		for ($i=1; $i<=31; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == date('j')) echo ' selected>';
			else echo '>';
			echo $day['pron'][$i];
		}
		echo '</select>';
		echo ' of ';
		
		echo '<select name="onetime_month">';
		for ($i=1; $i<=12; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == date('n')) echo ' selected>';
			else echo '>';
			echo $month['long'][$i];
		}
		echo '</select> ';
		
		echo '<select name="onetime_year">';
		for ($i=0; $i<3; $i++) echo '<option value="'.(date('Y')+$i).'">'.(date('Y')+$i);
		echo '</select> ';
		
		echo '<a href="javascript:wnd_url(\'calendarpicker.php\',300,200)"><img src="icons/calendar.png" width=16 height=16 title="Pick a date..."></a>';
		
		echo '</div>';

		echo '<br>';


		echo '<div id="layer_duration_n_start_picker" class="msg_success">';
		echo 'Keep reminding me about this event<br>';
		echo '<input type="radio" name="durationtype" value="untilcancelled" checked> Until cancelled<br>';
		echo '<input type="radio" name="durationtype" value="specified"> For the next <input type="text" name="duration" value="5" size=2> years<br>';
		echo '<br>';

		echo 'Starting from the ';
		echo '<select name="start_day">';
		for ($i=1; $i<=31; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == date('j')) echo ' selected>';
			else echo '>';
			echo $day['pron'][$i];
		}
		echo '</select>';
		echo ' of ';
		
		echo '<select name="start_month">';
		for ($i=1; $i<=12; $i++) {
			echo '<option value="'.$i.'"';
			if ($i == date('n')) echo ' selected>';
			else echo '>';
			echo $month['long'][$i];
		}
		echo '</select> ';
		
		echo '<select name="start_year">';
		for ($i=0; $i<3; $i++) echo '<option value="'.(date('Y')+$i).'">'.(date('Y')+$i);
		echo '</select> ';
		
		echo '<a href="javascript:wnd_url(\'calendarpicker.php\',300,200)"><img src="icons/calendar.png" width=16 height=16 title="Pick a date..."></a>';
		echo '</div>';

		echo '<br>';

		echo '<input type="submit" class="button" value="Continue">';
		echo '</form>';

	}

	include('design_foot.php');
?>