<?php
/**
 * wrapper to load internal views
 */

// view = name of view in core/views/
if (!is_alphanumeric($this->view)) {
    dp('HACK user '.$session->id.' attempted to use load view: '.$this->view);
    die(':-P');
}

$file = $page->getCoreDevInclude().'views/'.$this->view.'.php';
if (!file_exists($file))
    throw new Exception ('DEBUG: view not found '.$file);

$view = new ViewModel($file);
$view->registerVar('owner', $this->owner);
$view->registerVar('child', $this->child);
$view->registerVar('child2', $this->child2);
echo $view->render();

?>
