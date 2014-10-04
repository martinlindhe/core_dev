<?php
/**
 * details of an uploaded user photo
 */

//TODO: ability to edit description of photo
//TODO: add comments to photo

namespace cd;

switch ($this->owner) {
case 'show':
    // child = id

    $f = File::get($this->child);
    if (!$f)
        die('MECKLPSP');

    echo '<h1>Photo details for '.$f->name.'</h1>';

//d($f);

    $size = getimagesize( File::getUploadPath($this->child) );
//    d($size);
    echo 'Name: '.$f->name.'<br/>';
    echo 'Uploaded: '.ago($f->time_uploaded).' by '.$f->uploader.'<br/>';
    echo 'Resolution: '.$size[0].'x'.$size[1].'<br/>';
    echo 'Size: '.byte_count($f->size).'<br/>';
    echo '<br/>';

    // shows the photo
    $a = new XhtmlComponentA();
    $a->href = getThumbUrl($f->id, 0, 0);
    $a->rel  = 'lightbox';
    $a->content = showThumb($f->id, $f->name, 150, 150);
    echo $a->render();

    $lb = new YuiLightbox();
    echo $lb->render().'<br/>';

    if ($session->id && $session->id != $f->uploader)
        echo '&raquo; '.ahref('u/report/photo/'.$f->id, 'Report photo').'<br/>';

    if ($session->id && $session->id == $f->uploader) {
        echo '&raquo; '.ahref('u/photo/rotate/'.$f->id.'/90', 'Rotate left').'<br/>';
        echo '&raquo; '.ahref('u/photo/rotate/'.$f->id.'/270', 'Rotate right').'<br/>';
        echo '<br/>';
        echo '&raquo; '.ahref('u/photo/delete/'.$f->id, 'Delete photo').'<br/>';
    }
    echo '<br/>';

    $view = new ViewModel('views/user/rate.php');
    $view->registerVar('view', 'handle');
    $view->registerVar('owner', FILE);
    $view->registerVar('child', $f->id);
    echo 'Rate photo:<br/>';
    echo $view->render();
    break;

case 'rotate':
    // child = file id
    // child2 = rotate %

    $allowed = array(90, 270);
    if (!in_array($this->child2, $allowed)) {
        dp('HACK: odd rotate %: '.$this->child2);
        return;
    }

    $session->requireLoggedIn();
    $f = File::get($this->child);
    if ($session->id != $f->uploader) {
        dp('HACK: tried to rotate photo '.$this->child.' which is not uploaded by user '.$session->id);
        return;
    }

    $im = new ImageRotator($f);

    $im->rotate($this->child2);
    $im->render( $im->mimetype, File::getUploadPath($f->id) );
    File::sync($fileId); //updates tblFiles.size
    js_redirect('u/photo/show/'.$f->id);
    break;

case 'delete':
    $session->requireLoggedIn();
    if ($this->child && confirmed('Are you sure you want to delete this photo?')) {

        // verify that the owner of the album is current session id
        $im = File::get($this->child);
        if ($im->uploader != $session->id) {
            dp('HACK: tried to delete photo '.$this->child.' which is not uploaded by user '.$session->id);
            return;
        }

        File::delete($this->child);
        js_redirect('u/album/show/'.$session->id.'/'.$im->category);
    }
    break;

default:
    echo 'no such view: '.$this->owner;
}
