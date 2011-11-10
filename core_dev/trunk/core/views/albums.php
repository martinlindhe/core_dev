<?php
//TODO: close up view on a photo
//TODO: ability to rotate an uploaded photo

//TODO: ability to delete a photo
//TODO: ability to delete an empty photo album

//TODO later: move "create system wide albums" to admin panel

require_once('PhotoAlbum.php');

require_once('Image.php'); // for showThumb()
 

if (!$session->id)
    die('XXX gb only for logged in users');

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
        echo showThumb($im->id, $im->name, 50, 50).'<br/>';
    }

    if ($session->id == $this->child)
        echo '&raquo; '.ahref('iview/albums/upload/'.$this->child2, 'Upload photo').'<br/>';
    
    break;

case 'upload':
    // child = album id

    function handleUpload($p)
    {
        $session = SessionHandler::getInstance();
        
        $fileId = FileHelper::import(USER, $p['img'], $p['album']);
        
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
