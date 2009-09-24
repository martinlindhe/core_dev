<?php

require_once('config.php');
require('design_head.php');

echo xhtmlMenu($profile_menu, 'blog_menu');

showBlog();

require('design_foot.php');
?>
