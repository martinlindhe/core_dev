<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin FAQ<br/><br/>';
	
	if (empty($config['faq']['enabled'])) {
		echo 'FAQ module is not enabled.<br/>';
		require($project.'design_foot.php');
		die;
	}

	if (!empty($_POST['faq_q']) && isset($_POST['faq_a'])) {
		addFAQ($_POST['faq_q'], $_POST['faq_a']);
	}
	
	showFAQ();
	echo '<br/>';

	echo '<form method="post" action="">';
	echo 'Add new question: <input type="text" name="faq_q" size="40"/><br/>';
	echo 'Answer:<br/>';
	echo '<textarea name="faq_a" rows="8" cols="40"></textarea><br/>';
	echo '<input type="submit" class="button" value="Add"/>';
	echo '</form>';

	require($project.'design_foot.php');
?>