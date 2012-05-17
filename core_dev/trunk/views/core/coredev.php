<?php

//TODO: drop this file when all selftest-url checkers are updated

switch ($this->view) {
case 'selftest':
    $view = new ViewModel(__DIR__.'/selftest.php');
    echo $view->render();
    return;

default:
    throw new Exception ('no such view: '.$this->view);
}

?>
