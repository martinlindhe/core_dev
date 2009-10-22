<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('class.Comment.php');

require_once('/var/www/fmf/sitewatch/config.php');

$c = new Comment(Comment::BLOG);
$c->setOwner(0);


$c->setId(1);

echo $c->render();

//$c->newComment('hej hej!');



?>
