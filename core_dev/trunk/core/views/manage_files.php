<?php
/**
 * File manager
 */

// XXX: verify before deleting file!

//TODO: limit selection by selecting image type, dates, etc
//TODO: pagination

$session->requireAdmin();


if (!empty($_GET['unlink'])) {
    File::unlink($_GET['unlink']);
}

echo '<h1>All uploaded files</h1>';

$list = File::getList();

foreach ($list as $f)
{
    $view = new ViewModel('views/file_details.php');
    $view->registerVar('owner', $f->id);
    echo $view->render();
    echo '<br/>';
    echo '&raquo; '.ahref('?unlink='.$f->id, 'Permanently delete file').'<br/>';
    echo '<br/>';

}

?>
