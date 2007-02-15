<?
	//Displays track point overview with a calendar module to select a time period to show more details from
	//
	//Accepts parameters:
	// $_GET['id'] - ID of track point to show

	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$trackId = $_GET['id'];
	$trackPoint = getTrackPoint($db, $trackId);

	if (!$trackPoint) {
		header('Location: '.$config['start_page']);
		die;
	}

	$siteId = $trackPoint['siteId'];

	include('design_head.php');

	echo '<h2>Track point overview</h2>';

	echo 'Overview of track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';

	echo '<br>';
	echo MakeTrackerBox('Notes', $trackPoint['trackerNotes']).'<br>';

	//Use the timestamp when this trackId was created
	$date_first = strtotime($trackPoint['timeCreated']);

	$oldest_year = date('Y', $date_first);

	//rita tabell över aktuell månad, med länk till översikt för varje dag
	if (!empty($_GET['y']) && is_numeric($_GET['y'])) $display_year = $_GET['y'];
	else $display_year = date('Y');

	if (!empty($_GET['m']) && is_numeric($_GET['m'])) $display_month = $_GET['m'];
	else $display_month = date('n');

	$month_start = mktime(0, 0, 0, $display_month, 1, $display_year);
	$days_in_month = date('t', $month_start);
	$month_end = mktime(0, 0, 0, $display_month, $days_in_month, $display_year);

	$arr = getTrackerMonthOverview($db, $trackId, $display_year, $display_month);
	
	echo '<div class="cal_holder">';

		if ($date_first < $month_start) {
			$hover = 'onmouseover="domTT_activate(this, event, \'content\', \'Show previous month\', \'trail\', true);"';
			echo '<div class="cal_arrow_left" '.$hover.' onClick="location.href=\''.$_SERVER['PHP_SELF'].'?id='.$trackId.'&amp;y='.$display_year.'&amp;m='.($display_month-1).'\'"></div>';
		} else {
			echo '<div class="cal_arrow_leftgray"></div>';
		}
		echo '<div class="cal_header">'.$config['time']['month']['long'][$display_month].' '.$display_year.'</div>';

		if ($month_end < time()) {
			$hover = 'onmouseover="domTT_activate(this, event, \'content\', \'Show next month\', \'trail\', true);"';
			echo '<div class="cal_arrow_right" '.$hover.' onClick="location.href=\''.$_SERVER['PHP_SELF'].'?id='.$trackId.'&amp;y='.$display_year.'&amp;m='.($display_month+1).'\'"></div>';
		} else {
			echo '<div class="cal_arrow_rightgray"></div>';
		}

		echo '<div class="cal_holder_body">';
			echo '<div class="cal_weekhead">W</div>';
			echo '<div class="cal_weekday">M</div>';
			echo '<div class="cal_weekday">Tu</div>';
			echo '<div class="cal_weekday">W</div>';
			echo '<div class="cal_weekday">Th</div>';
			echo '<div class="cal_weekday">F</div>';
			echo '<div class="cal_weekday">Sa</div>';
			echo '<div class="cal_weekday">Su</div>';
			
			$first_in_month = mktime(0, 0, 0, $display_month, 1, $display_year);
			$start_weekday = date('N', $first_in_month);

			$weeknum = date('W', $first_in_month);
			
			$week_start = mktime(0, 0, 0, $display_month, 2-$start_weekday, $display_year);

			//select the whole week containing the 1:st of current month
			$hover = 'onmouseover="domTT_activate(this, event, \'content\', \'Select Week '.$weeknum.'\', \'trail\', true); HighlightWeek('.$weeknum.');" onmouseout="domTT_deactivate(this.id); UnHighlightWeek('.$weeknum.')"';
			echo '<div class="cal_weeknum" '.$hover.' onClick="Cal_Select('.$trackId.','.$week_start.',\'w\')">'.$weeknum.'</div>';

			if ($start_weekday) {
				//Place holders for days of the previous month
				
				//fixme: det här e ganska hackigt, går det koda enklare?
				$stamp = mktime(0, 0, 0, $display_month-1, 1, $display_year);
				$days_in_prevmonth = date('t', $stamp);

				for ($i=($start_weekday-1); $i>0; $i--) {

					$stamp = mktime(0, 0, 0, $display_month-1, $days_in_prevmonth - $i + 1, $display_year);
					
					$day = date('j', $stamp);
					echo '<div class="cal_day_othermonth">'.$day.'</div>';
				}
			}

			//Fill calendar with the days of current month
			for ($i=1; $i<=$days_in_month; $i++)
			{
				$stamp = mktime(0, 0, 0, $display_month, $i, $display_year);

				$curr_weekday = date('N', $stamp);
				$weeknum_new = date('W', $stamp);
				
				$curr_divname = 'week'.$weeknum_new.'_'.$curr_weekday;

				if ($weeknum != $weeknum_new) {
					$weeknum = $weeknum_new;
					
					$week_start = mktime(0, 0, 0, $display_month, $i, $display_year);

					$hover = 'onmouseover="domTT_activate(this, event, \'content\', \'Select Week '.$weeknum.'\', \'trail\', true); HighlightWeek('.$weeknum.');" onmouseout="domTT_deactivate(this.id); UnHighlightWeek('.$weeknum.')"';
					echo '<div class="cal_weeknum" '.$hover.' onClick="Cal_Select('.$trackId.','.$week_start.',\'w\')">'.$weeknum.'</div>';
				}

				$time_start = mktime(0, 0, 0, $display_month, $i, $display_year);

				if (!empty($arr) && in_array($i, $arr)) {
					$hover = 'onmouseover="domTT_activate(this, event, \'content\', \'Select '.getShortDate($time_start).'\', \'trail\', true);"';
					echo '<div id="'.$curr_divname.'" class="cal_day_thismonth_data" '.$hover.' onClick="Cal_Select('.$trackId.','.$time_start.',\'d\')">'.$i.'</div>';
				} else {
					echo '<div class="cal_day_thismonth_empty">'.$i.'</div>';
				}

			}

			//Place holders for days of the next month
			for ($i=1; $i<=(7-$curr_weekday); $i++) {
				echo '<div class="cal_day_othermonth">'.$i.'</div>';
			}
		echo '</div>';	/* end cal_holder_body */
		
		//time selector thingy
		echo '<div class="cal_ts_holder">';
			echo '<div class="cal_ts_header">Time selector</div>';
			echo '<div id="cal_ts_expander" class="cal_piece_expand" onClick="toggle_class(\'cal_ts_expander\',\'cal_piece_expand\',\'cal_piece_shrink\'); toggle_element_by_name(\'cal_ts_bodyor\')"></div>';
			
			echo '<div id="cal_ts_bodyor" class="cal_ts_body" style="display: none">';

				echo '<div id="cal_ts_object_predefined" class="cal_ts_object_grayed" onClick="TS_SelectPredefined()">';
					echo '<div class="cal_ts_radio">';
						echo '<input type="radio" id="timetype_timespan"> ';
					echo '</div>';
					
					echo '<div class="cal_ts_selection">';
						echo '<select id="timespan" disabled>';
						echo '<option>&nbsp;';

						$temp_from = mktime(0, 0, 0, date('n')-2, 1, date('Y')); //2 månader sen, den 1:a 00:00
						echo '<option onClick="Cal_Select('.$trackId.','.$temp_from.',\'m\')">'.formatShortMonth($temp_from);

						$temp_from = mktime(0, 0, 0, date('n')-1, 1, date('Y')); //Förra månaden den 1:a 00:00
						echo '<option onClick="Cal_Select('.$trackId.','.$temp_from.',\'m\')">'.formatShortMonth($temp_from);

						$temp_from = mktime(0, 0, 0, date('n'), 1, date('Y')); //Denna månaden den 1:a 00:00
						echo '<option onClick="Cal_Select('.$trackId.','.$temp_from.',\'m\')">'.formatShortMonth($temp_from);

						echo '</select>';
					echo '</div>';	/* end cal_ts_selection */
				echo '</div>'; /* end cal_ts_object_predefined */

				$max_year = date('Y');
			
				$time_from = mktime(0, 0, 0, 1, 1, date('Y'));
				$time_to = time();

				$from_year_selected = date('Y', $time_from);
				$from_month_selected = date('n', $time_from);
				$from_day_selected = date('j', $time_from);
			
				$to_year_selected = date('Y', $time_to);
				$to_month_selected = date('n', $time_to);
				$to_day_selected = date('j', $time_to);
			
				echo '<div id="cal_ts_object_freeform" class="cal_ts_object_grayed" onClick="TS_SelectFreeform('.$trackId.');">';
					echo '<div class="cal_ts_radio">';
						echo '<input type="radio" id="timetype_freeform"> ';
					echo '</div>';

					echo '<div class="cal_ts_selection">';
			
						echo '<select id="from_year" disabled onChange="Cal_FreeSelect('.$trackId.')">';
						for ($i=$oldest_year; $i<=$max_year; $i++) {
							echo '<option value="'.$i.'"';
							if ($i == $from_year_selected) echo ' selected';
							echo '>'.$i;
						}
						echo '</select> ';
					
						echo '<select id="from_month" disabled onChange="Cal_FreeSelect('.$trackId.')">';
						for ($i=1; $i<=12; $i++) {
							echo '<option value="'.$i.'"';
							if ($i == $from_month_selected) echo ' selected';
							echo '>'.$config['time']['month']['short'][$i];
						}
						echo '</select> ';
					
						echo '<select id="from_day" disabled onChange="Cal_FreeSelect('.$trackId.')">';
						for ($i=1; $i<=31; $i++) {
							echo '<option value="'.$i.'"';
							if ($i == $from_day_selected) echo ' selected';
							echo '>'.$i;
						}
						echo '</select>';
						
						echo '<br>';
					
						echo '<select id="to_year" disabled onChange="Cal_FreeSelect('.$trackId.')">';
						for ($i=$oldest_year; $i<=$max_year; $i++) {
							echo '<option value="'.$i.'"';
							if ($i == $to_year_selected) echo ' selected';
							echo '>'.$i;
						}
						echo '</select> ';
					
						echo '<select id="to_month" disabled onChange="Cal_FreeSelect('.$trackId.')">';
						for ($i=1; $i<=12; $i++) {
							echo '<option value="'.$i.'"';
							if ($i == $to_month_selected) echo ' selected';
							echo '>'.$config['time']['month']['short'][$i];
						}
						echo '</select> ';
					
						echo '<select id="to_day" disabled onChange="Cal_FreeSelect('.$trackId.')">';
						for ($i=1; $i<=31; $i++) {
							echo '<option value="'.$i.'"';
							if ($i == $to_day_selected) echo ' selected';
							echo '>'.$i;
						}
						echo '</select>';
					echo '</div>'; /* end cal_ts_selection */
				echo '</div>'; /* end cal_ts_object_freeform */

			echo '</div>'; /* end cal_ts_bodyor */
		echo '</div>'; /* end cal_ts_holder */


		//div för att visa "current selection", med lite data & länkar för mer info
		echo '<div id="cal_selection_holder" style="display: none;">';
			echo '<div class="cal_selection_header">Current selection</div>';
			echo '<div id="cal_selection_expander" class="cal_piece_shrink" onClick="toggle_class(\'cal_selection_expander\',\'cal_piece_expand\',\'cal_piece_shrink\'); toggle_element_by_name(\'cal_selection_body\')"></div>';

			echo '<div id="cal_selection_body">';
				//echo 'nothing to see here';
			echo '</div>';

		echo '</div>';

	echo '</div>'; /* end cal_holder */

	include('design_foot.php');
?>