<?php
/**
 * File manager
 */

// XXX: verify before deleting file!

//TODO: limit selection by selecting image type, dates, etc
//TODO: pagination

$session->requireAdmin();


if (!empty($_GET['delete'])) {
    FileHelper::delete($_GET['delete']);
}

echo '<h1>All uploaded files</h1>';

$list = File::getList();

foreach ($list as $f)
{
    echo '<h2>File details</h2>';
    echo 'Name: '.$f->name.'<br/>';
    echo 'Type: '.$f->type.'<br/>';
    echo 'Size: '.byte_count($f->size).'<br/>';
//    echo 'Mimetype: '.$f->mimetype.'<br/>';
    echo 'Uploaded: '.$f->time_uploaded.'<br/>';
    echo '&raquo; '.ahref('?delete='.$f->id, 'Delete file').'<br/>';
//    d($f);


    switch ($f->mimetype) {
    case 'audio/wav':   // works in FF6, Chrome, Safari, Opera
    case 'audio/mp3':   // works in IE9, Chrome, Safari, NOT IN FF6
    case 'audio/ogg':   // works in FF6, Chrome, Safari, Opera
    case 'audio/x-m4a': // works in Chrome, NOT IN FF6, ??? in IE9
        $a = new XhtmlComponentAudio();
        $a->src = '/coredev/file/'.$f->id;
        echo $a->render();
        break;

    case 'image/jpeg':
        $i = new XhtmlComponentImage();
        $i->title = $f->name;
        $i->src = '/coredev/file/'.$f->id;
        $i->width = 250;
        echo $i->render();
        break;

    default:
        echo 'SKIPPING: '.$f->mimetype.'<br/>';
        // d($f);
    }
    echo '<br/><br/>';
}

?>
