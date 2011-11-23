<?php
/**
 * For normal users usage of chatrooms
 */

$session->requireLoggedIn();

switch ($this->owner) {
case 'list':
    echo '<h2>Välj chattrum</h2>';

    $list = ChatRoom::getList();

    foreach ($list as $cr)
        echo ahref('iview/chatroom/chat/'.$cr->id, $cr->name).'<br/>';

    break;

case 'update':
    // XHR - returns last 30 msg from chatroom
    if ($this->child == 'new') {
        // XXX implement: only return the new messages (since previous call)
    }

    $page->setMimeType('text/plain');

    echo "aaa=123";
    break;

case 'chat':
    // child = room id

    function chatSubmit($p)
    {
        $session = SessionHandler::getInstance();

        $cr = ChatRoom::get($p['room']);
        if ($cr->locked_by)
            return false;

        $m = new ChatMessage();
        $m->room = $p['room'];
        $m->from = $session->id;
        $m->msg = $p['msg'];
        $m->microtime = microtime(true);
        ChatMessage::store($m);

        js_redirect('chatroom/chat/'.$p['room']);
    }

    $cr = ChatRoom::get($this->child);

    echo '<h2>Chatta i '.$cr->name.'</h2>';

    $msgs = ChatMessage::getRecent($this->child, 50);

    foreach ($msgs as $m) {
        $user = User::get($m->from);
        echo sql_time($m->microtime).' by '.ahref('iview/profile/'.$user->id, $user->name).': ';
        echo $m->msg.'<br/>';
    }

    if ($cr->locked_by) {
        echo 'Chatten är låst!';
        return;
    }

    $form = new XhtmlForm();
    $form->addInput('msg', 'Msg');
    $form->addHidden('room', $this->child);  // XXX hack
    $form->addSubmit('Send');
    $form->setHandler('chatSubmit');
    echo $form->render();

    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
