<?php

die; // XXX cant be tested without db...

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('PollWidget.php');
require_once('PollManager.php');

if ($session->isAdmin) {
    echo '<h1>Edit polls</h1>';

    $man = new PollManager();
    echo $man->render();
}

echo PollWidget::renderPoll(SITE, 1);

?>
