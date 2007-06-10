<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	if (!empty($_POST['poll_q']) && !empty($_POST['poll_dur'])) {
		$pollId = addPoll(POLL_SITE, 0, $_POST['poll_q'], $_POST['poll_dur']);
		
		for ($i=1; $i<=5; $i++) {
			if (!empty($_POST['poll_a'.$i])) {
				addCategory(CATEGORY_POLL, $_POST['poll_a'.$i], $pollId);
			}
		}
	}

	echo '<h1>Site polls</h1>';
	
	$list = getPolls(POLL_SITE);
	foreach ($list as $row) {
		showPoll($row);
	}

	echo 'Create a new weekly poll:<br/>';
	echo '<form method="post" action="">';
	echo 'Poll question:<br/>';
	echo '<input type="text" name="poll_q" size="30"/><br/>';
	echo '<b>The new poll will automatically start when the currently running poll ends</b><br/>';
	echo 'Duration of the poll: ';
	echo '<select name="poll_dur">';
	echo '<option value="day">1 day</option>';
	echo '<option value="week" selected="selected">1 week</option>';
	echo '<option value="month">1 month</option>';
	echo '</select><br/>';
	
	echo 'Answer 1: <input type="text" size="30" name="poll_a1"/><br/>';
	echo 'Answer 2: <input type="text" size="30" name="poll_a2"/><br/>';
	echo 'Answer 3: <input type="text" size="30" name="poll_a3"/><br/>';
	echo 'Answer 4: <input type="text" size="30" name="poll_a4"/><br/>';
	echo 'Answer 5: <input type="text" size="30" name="poll_a5"/><br/>';

	echo '<input type="submit" class="button" value="Create"/>';
	echo '</form>';

	require($project.'design_foot.php');
?>