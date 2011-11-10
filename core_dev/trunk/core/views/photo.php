<?php
/**
 * details of an uploaded user photo
 */

//TODO: ability to rotate an uploaded photo
//TODO: ability to edit description of photo
//TODO: add comments to photo

require_once('Image.php'); // for getThumbUrl()
require_once('YuiLightbox.php');

switch ($this->owner) {
case 'show':
    // child = id

    $im = File::get($this->child);

    echo '<h1>Photo details for '.$im->name.'</h1>';
    
d($im);

    // shows the photo
    $a = new XhtmlComponentA();
    $a->href = getThumbUrl($im->id, 0, 0);
    $a->rel  = 'lightbox';
    $a->content = showThumb($im->id, $im->name, 150, 150);
    echo $a->render();

    $lb = new YuiLightbox();
    echo $lb->render().'<br/>';

    if ($session->id && $session->id == $im->uploader)
        echo '&raquo; '.ahref('iview/photo/delete/'.$im->id, 'Delete photo');
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
