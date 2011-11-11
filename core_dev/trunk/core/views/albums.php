<?php

//TODO later: move "create system wide albums" to admin panel

require_once('PhotoAlbum.php');

require_once('Image.php'); // for showThumb()
require_once('ImageResizer.php');

require_once('YuiLightbox.php');

switch ($this->owner) {
case 'overview':
    // shows the users photo albums
    // child = user id, if set
    if ($this->child)
        $user_id = $this->child;
    else
        $user_id = $session->id;

    $user = User::get($user_id);

    echo '<h1>Photo albums for '.$user->name.'</h1>';

    // lists all albums (system + personal)
    $albums = PhotoAlbum::getByOwner($user_id);

    foreach ($albums as $album) {
        // TODO: show number of pics in each album
        echo ahref('iview/albums/show/'.$user_id.'/'.$album->id, $album->name);
        if (!$album->owner) echo ' (global)';
        echo '<br/>';
    }

    echo '<br/>';

    if ($user_id == $session->id)
        echo '&raquo; '.ahref('iview/albums/new', 'New album');

    break;

case 'show':
    // shows an user album
    // child = user id
    // child2 = album id

    if (!$this->child || !$this->child2)
        die('XXX wrong album params');

    $user = User::get($this->child);
    if (!$user)
        die('XXX NO SUCH USER');

    $album = PhotoAlbum::get($this->child2);
    if ($album->owner != 0 && $album->owner != $this->child)
        throw new Exception ('epic HACK attempt');

    echo '<h1>Photo album '.$album->name.' by user #'.$this->child.'</h1>';
    d($album);

    // shows album content
    $images = File::getByCategory(USER, $this->child2);

    foreach ($images as $im) {
        $a = new XhtmlComponentA();
        $a->href = getThumbUrl($im->id, 0, 0);
        $a->rel  = 'lightbox[album]';
        $a->content = showThumb($im->id, $im->name, 150, 150);
        echo $a->render();
        echo ahref('iview/photo/show/'.$im->id, 'Photo details');
        echo '<br/><br/>';
    }

    $lb = new YuiLightbox();
    echo $lb->render();


    if (!$images && $album->owner)
        echo '&raquo; '.ahref('iview/albums/delete/'.$this->child2, 'Delete empty album').'<br/>';

    if ($session->id == $this->child)
        echo '&raquo; '.ahref('iview/albums/upload/'.$this->child2, 'Upload photo').'<br/>';

    break;

case 'delete':
    $session->requireLoggedIn();
    if ($this->child && confirmed('Are you sure you want to delete this photo album?')) {

        // verify that the owner of the album is current session id
        $album = PhotoAlbum::get($this->child);
        if (!$album->owner || $album->owner != $session->id) {
            dp('HACK: tried to delete photo album '.$this->child.' which is not owned by user '.$session->id);
            return;
        }

        PhotoAlbum::delete($this->child);
        js_redirect('iview/albums/overview');
    }
    break;

case 'upload':
    // child = album id
    $session->requireLoggedIn();

    function handleUpload($p)
    {
        $session = SessionHandler::getInstance();
        $error   = ErrorHandler::getInstance();

        switch ($p['img']['type']) {
        case 'image/jpeg': break;
        case 'image/png': break;
        case 'image/gif': break;
        default:
            $error->add('Uploaded file is not an image = '.$p['img']['type']);
            return false;
        }

        $fileId = File::import(USER, $p['img'], $p['album']);

        $im = new ImageResizer( File::get($fileId) );

        // FIXME: make these configurable
        $max_width = 800;
        $max_height = 800;

        if ($im->width > $max_width || $im->height > $max_height) {
            $im->resizeAspect($max_width, $max_height);
            $im->render( $im->mimetype, File::getUploadPath($fileId) );
            File::sync($fileId); //updates tblFiles.size
        }

        js_redirect('iview/albums/show/'.$session->id.'/'.$p['album']);
    }

    if (!$this->child)
        die('XXX no album specified');

    $album = PhotoAlbum::get($this->child);
    if ($album->owner != 0 && $album->owner != $session->id)
        throw new Exception ('epic HACK attempt');

    echo '<h1>Upload photo to album '.$album->name.'</h1>';

    $form = new XhtmlForm();

    $form->addHidden('album', $this->child);
    $form->addFile('img', 'Select file');

    $form->addSubmit('Save');
    $form->setHandler('handleUpload');

    echo $form->render();
    break;

case 'new':
    $session->requireLoggedIn();
    // create new photo album
    echo '<h1>Create a new photo album</h1>';

    function handleNew($p)
    {
        $session = SessionHandler::getInstance();

        $o = new PhotoAlbum();
        $o->owner        = $session->id;
        $o->name         = $p['name'];
        $o->time_created = sql_datetime( time() );

        if ($session->isSuperAdmin && $p['system'])
            $o->owner = 0; // create a system wide album

        PhotoAlbum::store($o);

        js_redirect('iview/albums/overview');
    }

    $form = new XhtmlForm();
    $form->addInput('name', 'Name');

    if ($session->isSuperAdmin)
        $form->addCheckbox('system', 'System wide album? (SUPERADMIN)');

    $form->addSubmit('Save');
    $form->setHandler('handleNew');
    echo $form->render();
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
