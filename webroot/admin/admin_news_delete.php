<?
	require_once('find_config.php');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$newsId = $_GET['id'];

	$session->requireAdmin();

	if (confirmed('Are you sure you wish to delete this news entry?', 'id', $newsId)) {
		removeNews($newsId);
		header('Location: admin_news.php'.getProjectPath(false));
	}

?>