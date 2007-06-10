<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	if (!empty($_POST['poll_q']) && !empty($_POST['poll_dur']) && !empty($_POST['poll_start'])) {
		$pollId = addPoll(POLL_SITE, 0, $_POST['poll_q'], $_POST['poll_dur'], $_POST['poll_start']);
		
		for ($i=1; $i<=5; $i++) {
			if (!empty($_POST['poll_a'.$i])) {
				addCategory(CATEGORY_POLL, $_POST['poll_a'.$i], $pollId);
			}
		}
	}

	echo '<h1>Site polls</h1>';
	
	$list = getPolls(POLL_SITE);
	echo '<table>';
	echo '<tr>';
	echo '<th>Title</th>';
	echo '<th>Starts</th>';
	echo '<th>Ends</th>';
	echo '</tr>';
	foreach ($list as $row) {
		$expired = $active = false;
		if (time() > datetime_to_timestamp($row['timeEnd'])) $expired = true;
		if (time() >= datetime_to_timestamp($row['timeStart']) && !$expired) $active = true;

		if ($expired) {
			echo '<tr style="font-style: italic">';
		} else if ($active) {
			echo '<tr style="font-weight: bold">';
		} else {
			echo '<tr>';
		}
		

		echo '<td>'.$row['itemText'].'</td>';
		echo '<td>'.$row['timeStart'].'</td>';
		echo '<td>'.$row['timeEnd'].'</td>';
		echo '</tr>';
	}
	echo '</table>';

	echo '<h2 onclick="toggle_element_by_name(\'new_poll_form\')">Add new weekly poll</h2>';
	echo '<div id="new_poll_form">'; // style="display:none">';
	echo '<form method="post" action="">';
	echo 'Question: ';
	echo '<input type="text" name="poll_q" size="30"/><br/>';

	echo 'Duration of the poll: ';
	echo '<select name="poll_dur">';
	echo '<option value="day">1 day</option>';
	echo '<option value="week" selected="selected">1 week</option>';
	echo '<option value="month">1 month</option>';
	echo '</select><br/>';

	echo 'Poll start: ';
	echo '<select name="poll_start">';
	echo '<option value="thismonday">this weeks monday</option>';
	echo '<option value="nextfree"'.(count($list)?' selected="selected"':'').'>next free time</option>';
	echo '</select><br/>';
	echo '<br/>';

	echo 'Answer 1: <input type="text" size="30" name="poll_a1"/><br/>';
	echo 'Answer 2: <input type="text" size="30" name="poll_a2"/><br/>';
	echo 'Answer 3: <input type="text" size="30" name="poll_a3"/><br/>';
	echo 'Answer 4: <input type="text" size="30" name="poll_a4"/><br/>';
	echo 'Answer 5: <input type="text" size="30" name="poll_a5"/><br/>';

	echo '<input type="submit" class="button" value="Create"/>';
	echo '</form>';
	echo '</div>';

	require($project.'design_foot.php');
?>