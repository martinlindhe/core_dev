<?php
/**
 * File manager
 */

//TODO: ability to permanently delete a file

//TODO: limit selection by selecting image type, dates, etc
//TODO: pagination

$session->requireAdmin();

echo '<h1>All uploaded files</h1>';

$list = File::getList();

foreach ($list as $f)
{
    switch ($f->mimetype) {
    case 'audio/wav':   // works in FF6, Chrome, Safari, Opera
    case 'audio/mp3':   // works in IE9, Chrome, Safari, NOT IN FF6
    case 'audio/ogg':   // works in FF6, Chrome, Safari, Opera
    case 'audio/x-m4a': // works in Chrome, NOT IN FF6, ??? in IE9
        echo '<h2>Audio</h2>';
        d($f);

        $a = new XhtmlComponentAudio();
        $a->src = '/coredev/file/'.$f->id;
        echo $a->render();
        break;

    case 'image/jpeg':
        echo '<h2>Image</h2>';
        d($f);

        $i = new XhtmlComponentImage();
        $i->title = $f->name;
        $i->src = '/coredev/file/'.$f->id;
        echo $i->render();
        break;

    default:
        echo 'SKIPPING: '.$f->mimetype.'<br/>';
        // d($f);
    }
}


?>
