<?php

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

echo xhtmlMenu($profile_menu, 'blog_menu');

wiki('Settings');
echo '<br/>';

editSettings();

require('design_foot.php');
?>
