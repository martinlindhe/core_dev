<?php

require_once('/var/www/core_dev/core/class.Comment.php');


$c = new Comment(Comment::NEWS);
$c->create(0, 'hej hej!');

$list = $c->children(0);

$c->delete(1);

print_r($c);


?>
