<?php

$session->requireLoggedIn();

switch ($this->view) {
case 'show':
    echo '<h1>Uploaded files</h1>';

    $files = new FileList(FILETYPE_PROCESS);
    $list = $files->get();

    foreach ($list as $row) {
        echo ahref('queue/status/'.$row['fileId'], $row['fileName']);

        echo ', mime='.$row['fileMime'].' uploaded '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
    }

    echo '<h1>Converted files</h1>';

    $files = new FileList(FILETYPE_CLONE_CONVERTED);
    $list = $files->get();

    foreach ($list as $row) {
        echo ahref('queue/status/'.$row['fileId'], 'Details');
        echo ', mime='.$row['fileMime'].' created '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
    }
    break;

case 'new':

    function uploadSubmit($p)
    {
        // XhtmlForm:s upload handler har redan processat filen här
        $eventId = TaskQueue::addTask(TASK_UPLOAD, $p['f1']);
        if (!$eventId) {
            echo 'file upload handling failed';
            return false;
        }

        echo '<div class="okay">Your file has been uploaded successfully!</div><br/>';
        echo ahref('queue/show/'.$eventId, 'Click here').' to perform further actions on this file.';
        return true;
    }

    echo 'Max allowed upload size is '.ini_get('upload_max_filesize').'<br/>';
    echo 'Max allowed POST size is '.ini_get('post_max_size').'<br/>';
    echo '<br/>';

    $form = new XhtmlForm('ul_pdf');
    $form->addFile('f1', 'Fil', USER);
    $form->addSubmit('Upload');
    $form->setHandler('uploadSubmit');
    echo $form->render();
    break;

default:
    echo 'No handler for view '.$this->view;

}

?>
