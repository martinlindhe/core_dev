<?php

// chatroom moderation

//TODO: ability to LOCK a chatroom so it cant be used
//TODO: ability to configure a chatroom to allow anonymous users

namespace cd;

$session->requireSuperAdmin();

switch ($this->owner) {
case 'list':
    echo '<h2>Existing chatrooms</h2>';
    echo '<br/>';

    $list = ChatRoom::getList();

    foreach ($list as $cr) {
        echo ahref('a/chatroom/edit/'.$cr->id, $cr->name);
        if ($cr->locked_by)
            echo ' locked by '.$cr->locked_by.', '.ago($cr->time_locked);
        echo '<br/>';
    }

    echo '<br/>';
    echo '&raquo; '.ahref('a/chatroom/new', 'New chatroom');
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
        $o->store();

        js_redirect('a/chatroom/list');
    }

    $o = ChatRoom::get($this->child);

    echo '<h2>Edit chatroom '.$o->name.'</h2>';

    $x = new XhtmlForm();
    $x->addHidden('roomid', $o->id); //XXX haxx
    $x->addInput('name', 'Name', $o->name, 200);
    $x->addCheckbox('locked', 'Lock chatroom (read only)', $o->locked_by ? 1 : 0);
    $x->addSubmit('Save');
    $x->setHandler('editHandler');
    echo $x->render();
    echo '<br/>';
    echo '&raquo; '.ahref('a/chatroom/empty/'.$this->child, 'Empty chatroom of messages').'<br/>';
    echo '<br/>';
    echo '&raquo; '.ahref('a/chatroom/remove/'.$this->child, 'Remove chatroom').'<br/>';
    break;

case 'remove':
    if (confirmed('Are you sure you want to remove this chatroom?')) {
        ChatRoom::remove($this->child);
        js_redirect('a/chatroom/list');
    }
    break;

case 'empty':
    if (confirmed('Are you sure you want to remove all messages from this chatroom?')) {
        ChatMessage::deleteByRoom($this->child);
        js_redirect('a/chatroom/list');
    }
    break;


case 'new':
    function createHandler($p)
    {
        $o = new ChatRoom();
        $o->name = trim($p['name']);
        $o->id   = $o->store();

        js_redirect('a/chatroom/list');
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
