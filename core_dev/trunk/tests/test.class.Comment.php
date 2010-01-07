<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('class.Comments.php');

die("FIXME: rewrite test to work on fake db");


require_once('/var/www/fmf/sitewatch/config.php');


$c = new CommentList(WIKI);
$c->setOwner($wiki->getId());
$c->setLimit(5);
$c->setAnonAccess(true);
//$c->disableCaptcha();
echo $c->render();


/*
$c = new CommentItem(BLOG);
$c->setOwner(0);


$c->setId(1);

echo $c->render();

//$c->newComment('hej hej!');
*/


?>
