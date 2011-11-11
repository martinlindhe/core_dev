<?php
/**
 * details of an uploaded user photo
 */

//TODO: ability to edit description of photo
//TODO: add comments to photo

//XXXX: show image dimensions on 'show' view

require_once('Image.php'); // for getThumbUrl()
require_once('ImageRotator.php');
require_once('YuiLightbox.php');

switch ($this->owner) {
case 'show':
    // child = id

    $f = File::get($this->child);

    echo '<h1>Photo details for '.$f->name.'</h1>';
    
d($f);

    // shows the photo
    $a = new XhtmlComponentA();
    $a->href = getThumbUrl($f->id, 0, 0);
    $a->rel  = 'lightbox';
    $a->content = showThumb($f->id, $f->name, 150, 150);
    echo $a->render();

    $lb = new YuiLightbox();
    echo $lb->render().'<br/>';

    if ($session->id && $session->id == $f->uploader) {
        echo '&raquo; '.ahref('iview/photo/rotate/'.$f->id.'/90', 'Rotate left').'<br/>';
        echo '&raquo; '.ahref('iview/photo/rotate/'.$f->id.'/270', 'Rotate right').'<br/>';
        echo '<br/>';
        echo '&raquo; '.ahref('iview/photo/delete/'.$f->id, 'Delete photo').'<br/>';
    }
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
    js_redirect('iview/photo/show/'.$f->id);  
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
        js_redirect('iview/albums/overview');
    }   
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
