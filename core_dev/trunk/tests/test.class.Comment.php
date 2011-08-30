<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Comments.php');

die("FIXME: rewrite test to work on fake db");


require_once('/var/www/fmf/sitewatch/config.php');


echo CommentViewer::render(WIKI, $wiki->getId() );



?>
