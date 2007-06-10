<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$pollId = $_GET['id'];
		
		if (!empty($_POST['poll_q'])) {
			updatePoll(POLL_SITE, $pollId, $_POST['poll_q']);

			$list = getCategories(CATEGORY_POLL, $pollId);
			for ($i=0; $i<count($list); $i++) {
				if (!empty($_POST['poll_a'.$i])) {
					updateCategory(CATEGORY_POLL, $list[$i]['categoryId'], $_POST['poll_a'.$i]);
				}
			}
		}
		
		if (isset($_GET['delete']) && confirmed('Are you sure you want to delete this site poll?', 'delete&amp;id', $pollId)) {
			removePoll(POLL_SITE, $pollId);
		}

		$poll = getPoll(POLL_SITE, $pollId);

		echo '<h1>Edit site poll</h1>';

		echo '<form method="post" action="">';
		echo 'Question: ';
		echo '<input type="text" name="poll_q" size="30" value="'.$poll['pollText'].'"/><br/>';

		echo 'Poll starts: '.$poll['timeStart'].'<br/>';
		echo 'Poll ends: '.$poll['timeEnd'].'<br/>';
		echo '<br/>';

		if ($poll) {
			$list = getCategories(CATEGORY_POLL, $pollId);
			for ($i=0; $i<count($list); $i++) {
				echo 'Answer '.($i+1).': <input type="text" size="30" name="poll_a'.$i.'" value="'.$list[$i]['categoryName'].'"/><br/>';
			}
		}

		echo '<input type="submit" class="button" value="Save changes"/>';
		echo '</form>';
		
		echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$pollId.'&amp;delete'.getProjectPath().'">Delete poll</a>';

		require($project.'design_foot.php');
		die;
	}

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

		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$row['pollId'].getProjectPath().'">'.$row['pollText'].'</a></td>';
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

	for ($i=1; $i<=5; $i++) {
		echo 'Answer '.$i.': <input type="text" size="30" name="poll_a'.$i.'"/><br/>';
	}

	echo '<input type="submit" class="button" value="Create"/>';
	echo '</form>';
	echo '</div>';

	require($project.'design_foot.php');
?>