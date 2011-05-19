<?php

$session->requireLoggedIn();

switch ($this->view) {
case 'show':
    echo '<h1>Uploaded files</h1>';

    $files = new FileList(FILETYPE_PROCESS);
    $list = $files->get();

    foreach ($list as $row) {
        echo '<a href="show_file_status.php?id='.$row['fileId'].'">'.$row['fileName'].'</a>';
        echo ', mime='.$row['fileMime'].' uploaded '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
    }

    echo '<h1>Converted files</h1>';

    $files = new FileList(FILETYPE_CLONE_CONVERTED);
    $list = $files->get();

    foreach ($list as $row) {
        echo '<a href="show_file_status.php?id='.$row['fileId'].'">Details</a>';
        echo ', mime='.$row['fileMime'].' created '.$row['timeUploaded'].' by '.Users::link($row['uploaderId']).'<br/>';
    }
    break;

case 'new':
    set_time_limit(60*10);    //10 minute max, for big uploads

    if (!empty($_FILES['file2'])) {
        $eventId = addProcessEvent(PROCESS_UPLOAD, $h->session->id, $_FILES['file2']);
        if ($eventId) {
            echo '<div class="okay">Your file has been uploaded successfully!</div><br/>';
            echo ahref('queue/show/'.$eventId, 'Click here').' to perform further actions on this file.';
            require('design_foot.php');
            die;
        } else {
            echo 'file upload handling failed';
        }
    }

    echo 'Max allowed upload size is '.ini_get('upload_max_filesize').'<br/><br/>';
    echo 'Max allowed POST size is '.ini_get('post_max_size').'<br/><br/>';

    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data">';
    echo '<input type="file" name="file2"/>';
    echo '<input type="submit" class="button" value="Upload"/>';
    echo '</form>';
    break;

default:
    echo 'No handler for view '.$this->view;

}

?>
