<?php

$meta_css[] = $config['app']['web_root'].'css/site.css';
createXHTMLHeader();
?>
<div id="top">
	<div id="top-logo"></div>
	<div id="top-items">
		<?=xhtmlForm()?>
		<a href="<?=$config['app']['web_root']?>wiki.php">Wiki</a>
		<a href="<?=$config['app']['web_root']?>new_issue.php">New issue</a>
		Search <?=xhtmlInput('search', '', 8)?>
		<?=xhtmlFormClose()?>
	</div>
</div>


<div id="leftmenu">
<?php

$menu = array(
	'index.php' => 'Home'
);
createMenu($menu);

if ($h->sess->id) {
	$menu = array(
		'x' => 'Projects',					//overview of all projects
		'issues.php?show=open' => 'Issues',	//overview of open issues
		'z' => 'My tasks'					//overview of my assigned tasks
	);
	createMenu($menu);

	if ($h->sess->isAdmin) {
		$menu = array(
			'manage/issue_categories.php' => 'Manage',
			$config['core']['web_root'].'admin/admin.php' => 'Admin'
		);
		createMenu($menu);
	}

	$menu = array(
		'?logout' => 'Logout'
	);
	createMenu($menu);

} else {
	$menu = array('?login' => 'Log in');
	createMenu($menu);
}
?>
</div>

<div id="middle">
