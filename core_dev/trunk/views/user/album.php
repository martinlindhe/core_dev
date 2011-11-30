<?php

//TODO later: move "create system wide albums" to admin panel

require_once('PhotoAlbum.php');

require_once('Image.php'); // for showThumb()
require_once('ImageResizer.php');
require_once('YuiLightbox.php');
require_once('Html5Uploader.php');

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
        echo ahref('u/album/show/'.$user_id.'/'.$album->id, $album->name);
        if (!$album->owner) echo ' (global)';
        echo '<br/>';
    }

    echo '<br/>';

    if ($user_id == $session->id)
        echo '&raquo; '.ahref('u/album/new', 'New album');

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
    echo 'Created '.ago($album->time_created).'<br/>';

    // shows album content
    $images = File::getByCategory(USER, $this->child2, $this->child);

    if (!$images && $album->owner)
        echo '&raquo; '.ahref('u/album/delete/'.$this->child2, 'Delete empty album').'<br/>';

    if ($session->id == $this->child)
        echo '&raquo; '.ahref('u/album/upload/'.$this->child2, 'Add photos').'<br/>';

    foreach ($images as $im) {
        $a = new XhtmlComponentA();
        $a->href = getThumbUrl($im->id, 0, 0);
        $a->rel  = 'lightbox[album]';
        $a->content = showThumb($im->id, $im->name, 150, 150);
        echo $a->render();
        echo ahref('u/photo/show/'.$im->id, 'Details');
        echo '<br/><br/>';
    }

    $lb = new YuiLightbox();
    echo $lb->render();
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
        js_redirect('u/album/overview');
    }
    break;

case 'upload':
    // child = album id
    $session->requireLoggedIn();

    function handleUpload($p)
    {
        $session = SessionHandler::getInstance();
//XXX SECURITY: verify that destination album is owned by current user
        $fileId = File::importImage(USER, $p['img'], $p['album']);
        if ($fileId)
            js_redirect('u/album/show/'.$session->id.'/'.$p['album']);

        return false;
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

    // only enable Html5Uploader for supported browsers
    $b = HttpUserAgent::getBrowser();
    if ($b->name == 'Firefox' || $b->name == 'Chrome')
        echo Html5Uploader::albumUploader($this->child);

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

        $album_id = PhotoAlbum::store($o);

        js_redirect('u/album/show/'.$session->id.'/'.$album_id);
    }

    $form = new XhtmlForm();
    $form->addInput('name', 'Name');
    $form->setFocus('name');

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
