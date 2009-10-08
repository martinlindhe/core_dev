<?php

//handle quick search:
if (!empty($_POST['qu'])) {
	$hit = Users::searchUsernameContains($_POST['qu']);
	if ($hit) {
		header('Location: user.php?id='.$hit);
		die;
	}
}

//fetch a random user:
if (isset($_GET['rand'])) Users::randomUserPage();

$header = new xhtml_header();
echo $header->render();

?>
<div id="header">
	<div id="header-logo">
		<img src="<?php echo $config['app']['web_root']?>gfx/logo.png" alt="Sample Site"/>
	</div>
	<div id="header-items">
		<a href="<?php echo $config['app']['web_root']?>wiki.php?Wiki:About">About</a>
		<a href="<?php echo $config['app']['web_root']?>wiki.php?Wiki:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?php

$menu = array(
	'index.php' => 'Home',
	'news.php' => 'News',
	'faq.php' => 'FAQ',
	'feedback.php' => 'Feedback',
	'forum.php' => 'Forum',
	'scribble.php' => 'Scribble',
	'blogs.php' => 'Blogs',
	'polls.php' => 'Polls',
	'users.php' => 'Users'
	//$config['core']['web_root'].'process/' => 'PROCESS SERVER'
);
echo xhtmlMenu($menu);

if ($session->isAdmin) {
	$menu = array(
		$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin');
	echo xhtmlMenu($menu);
}

if ($session->id) {
	$menu = array(
		'user.php' => 'My profile',
		'?logout' => 'Logout');
} else {
	$menu = array('login.php' => 'Log in');
}
echo xhtmlMenu($menu);
?>
<br/>
Quick search:<br/>
<form method="post" action="">
<input type="text" name="qu" size="12"> <a href="?rand">[R]</a><br/>
</form>
</div>

<div id="middle">
