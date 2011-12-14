<?php
/**
 * File manager
 */

// XXX: verify before deleting file!

//TODO: limit selection by selecting image type, dates, etc
//TODO: pagination

require_once('YuiDatatable.php');

$session->requireAdmin();



if (!$this->owner)
    $this->owner = 'list';

switch ($this->owner) {
case 'list':

    echo '<h1>All uploaded files</h1>';

    $list = File::getList();

    $dt = new YuiDatatable();
    $dt->addColumn('id',     '#', 'link', 'a/files/details/', 'name');
    $dt->addColumn('time_uploaded', 'Uploaded');
    $dt->addColumn('uploader',    'Uploader',       'link', 'u/profile/' );
    $dt->addColumn('type',    'Type');
    $dt->addColumn('size',    'Size');
    $dt->addColumn('mimetype', 'Mime');
    $dt->setDataSource( $list );
    echo $dt->render();
    break;

case 'delete':
    if (confirmed('Are you sure you want to permanently delete this file?')) {
        File::unlink($this->child);
        js_redirect('a/files/list');
    }
    break;

case 'details':
    // child = file id

    $view = new ViewModel('views/user/file_details.php');
    $view->registerVar('owner', $this->child);
    echo $view->render();
    echo '<br/>';
    echo '&raquo; '.ahref('a/files/delete/'.$this->child, 'Permanently delete file').'<br/>';
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
