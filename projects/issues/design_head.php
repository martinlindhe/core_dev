<?php

$header = new xhtml_header();
$header->addCss('css/site.css');
echo $header->render();

?>
<div id="top">
	<div id="top-logo"></div>
	<div id="top-items">
		<?php echo xhtmlForm()?>
		<a href="<?php echo $config['app']['web_root']?>wiki.php">Wiki</a>
		<a href="<?php echo $config['app']['web_root']?>new_issue.php">New issue</a>
		Search <?php echo xhtmlInput('search', '', 8)?>
		<?php echo xhtmlFormClose()?>
	</div>
</div>


<div id="leftmenu">
<?php

$menu = array(
	'index.php' => 'Home'
);
echo xhtmlMenu($menu);

if ($h->session->id) {
	$menu = array(
		'x' => 'Projects',					//overview of all projects
		'issues.php?show=open' => 'Issues',	//overview of open issues
		'z' => 'My tasks'					//overview of my assigned tasks
	);
	echo xhtmlMenu($menu);

	if ($h->session->isAdmin) {
		$menu = array(
			'manage/issue_categories.php' => 'Manage',
			$config['core']['web_root'].'admin/admin.php' => 'Admin'
		);
		echo xhtmlMenu($menu);
	}

	$menu = array(
		'?logout' => 'Logout'
	);
	echo xhtmlMenu($menu);

} else {
	$menu = array('?login' => 'Log in');
	echo xhtmlMenu($menu);
}
?>
</div>

<div id="middle">
