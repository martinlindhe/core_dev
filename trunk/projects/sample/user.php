<?php

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

createMenu($profile_menu, 'blog_menu');

Users::showUser('id');	//id is the GET parameter to pass the user ID to this script, if not specified, current user is shown

require('design_foot.php');
?>
