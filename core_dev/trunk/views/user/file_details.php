<?php
/**
 * Shows detailed info of one file
 */

namespace cd;

$fileId = $this->owner;

$f = File::get($fileId);
if (!$f)
    throw new \Exception ('file not found: '.$fileId);

echo '<h2>File details</h2>';
echo 'Name: '.$f->name.'<br/>';
echo 'Type: '.$f->type.'<br/>';
echo 'Size: '.byte_count($f->size).'<br/>';
echo 'Mimetype: '.$f->mimetype.'<br/>';
echo 'Uploaded: '.ago($f->time_uploaded).'<br/>';
echo '<br/>';
//d($f);


switch ($f->mimetype) {
case 'audio/wav':   // works in FF6, Chrome, Safari, Opera
case 'audio/x-wav':

case 'audio/mp3':   // works in IE9, Chrome, Safari, NOT IN FF6
case 'audio/ogg':   // works in FF6, Chrome, Safari, Opera
case 'audio/x-m4a': // works in Chrome, NOT IN FF6, ??? in IE9
    $a = new XhtmlComponentAudio();
    $a->src = 'c/file/'.$f->id;
    echo $a->render();
    break;

case 'image/jpeg':
case 'image/png':
case 'image/gif':
    $i = new XhtmlComponentImage();
    $i->title = $f->name;
    $i->src = 'c/file/'.$f->id;
    $i->width = 250;
    echo $i->render();
    break;

default:
    echo 'SKIPPING: '.$f->mimetype.'<br/>';
    // d($f);
}

echo '<br/>';

?>
