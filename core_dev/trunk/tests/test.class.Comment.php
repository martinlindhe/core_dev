<?php

require_once('/var/www/core_dev/trunk/core/class.Comment.php');

require_once('/var/www/fmf/sitewatch/config.php');

$c = new Comment(Comment::BLOG);
$c->setOwner(0);


$c->setId(1);

echo $c->render();

//$c->newComment('hej hej!');



?>
