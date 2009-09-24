<?php

require_once('config.php');
require('design_head.php');

echo xhtmlMenu($profile_menu, 'blog_menu');

showUserBlogs('id');	//id is the GET parameter name to pass the userId

require('design_foot.php');
?>
