<?php

// chatroom moderation

//TODO: ability to LOCK a chatroom so it cant be used
//TODO: ability to configure a chatroom to allow anonymous users

//TODO: delete chat rooms

$session->requireSuperAdmin();

switch ($this->owner) {
case 'list':
    echo '<h2>Existing chatrooms</h2>';
    echo '<br/>';

    $list = ChatRoom::getList();

    foreach ($list as $cr) {
        echo ahref('iview/chatrooms/edit/'.$cr->id, $cr->name);
        if ($cr->locked_by)
            echo ' locked by '.$cr->locked_by.', '.ago($cr->time_locked);
        echo '<br/>';
    }

    echo '<br/>';
    echo '&raquo; '.ahref('iview/chatrooms/new', 'New chatroom');
    break;

case 'edit':
    // child = room id
    function editHandler($p)
    {
        $o = new ChatRoom();
        $o->id = $p['roomid'];
        $o->name = trim($p['name']);

        if ($p['locked']) {
            $session = SessionHandler::getInstance();
            $o->locked_by = $session->id;
            $o->time_locked = sql_datetime( time() );
        }
        ChatRoom::store($o);

        js_redirect('iview/chatrooms/list');
    }

    $o = ChatRoom::get($this->child);

    echo '<h2>Edit chatroom '.$o->name.'</h2>';

    $x = new XhtmlForm();
    $x->addHidden('roomid', $o->id); //XXX haxx
    $x->addInput('name', 'Name', $o->name, 40);
    $x->addCheckbox('locked', 'Lock chatroom (read only)', $o->locked_by ? 1 : 0);
    $x->addSubmit('Save');
    $x->setHandler('editHandler');
    echo $x->render();
    break;

case 'new':
    // child = room id
    function createHandler($p)
    {
        $o = new ChatRoom();
        $o->name = trim($p['name']);
        ChatRoom::store($o);

        js_redirect('iview/chatrooms/list');
    }

    echo '<h2>Create new chatroom</h2>';

    $x = new XhtmlForm();
    $x->addInput('name', 'Name');
    $x->addSubmit('Create');
    $x->setHandler('createHandler');
    echo $x->render();
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
