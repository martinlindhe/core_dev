<?php
//TODO: move "create system wide albums" to admin panel
//XXX: ability to upload photo to an album

//XXX: ability to rotate an uploaded photo

//XXX show content of an album


require_once('PhotoAlbum.php');

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
        echo ahref('coredev/view/albums/show/'.$user_id.'/'.$album->id, $album->name);
        if (!$album->owner) echo ' (global)';
        echo '<br/>';
    }

    echo '<br/>';
    
    if ($user_id == $session->id)
        echo '&raquo; '.ahref('coredev/view/albums/new', 'New album');

    break;  

case 'show':
    // shows an user album
    // child = user id
    // child2 = album id
    
    $x = PhotoAlbum::get($this->child2);
    
    d($x);
    
    
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
        
        js_redirect('coredev/view/albums/overview');
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
