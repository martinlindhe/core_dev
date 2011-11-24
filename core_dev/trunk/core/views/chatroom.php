<?php
/**
 * For normal users usage of chatrooms
 */

require_once('ChatRoomUpdater.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'list':
    echo '<h2>Select chatroom</h2>';

    $list = ChatRoom::getList();

    foreach ($list as $cr)
        echo ahref('iview/chatroom/chat/'.$cr->id, $cr->name).'<br/>';

    break;

case 'update':
    // JSON - returns messages in chatroom (since ts)
    // child = room id
    if (!$this->child || !is_numeric($this->child))
        die('hey');

    $ts = 0;

    if (isset($_GET['ts']) && is_numeric($_GET['ts']))
        $ts = $_GET['ts'];

    //XXX OPTIMIZATION: strip room id from response
    // XXX TODO: inject username in response
    $res = ChatMessage::getRecent($this->child, $ts, 5);

    $page->setMimeType('text/plain');
    echo json_encode($res);
    break;

case 'chat':
    // child = room id

    function chatSubmit($p)
    {
        if (!$p['msg'])
            return;

        $session = SessionHandler::getInstance();

        $cr = ChatRoom::get($p['room']);
        if ($cr->locked_by)
            return false;

        $m = new ChatMessage();
        $m->room = $p['room'];
        $m->from = $session->id;
        $m->msg  = $p['msg'];
        $m->microtime = microtime(true);
        ChatMessage::store($m);

        js_redirect('iview/chatroom/chat/'.$p['room']);
    }

    $cr = ChatRoom::get($this->child);

    echo '<h2>Chat in '.$cr->name.'</h2>';

    if ($cr->locked_by) {
        echo 'The chatroom is locked!';
        return;
    }

    ChatRoomUpdater::init(); // registers the chatroom_init() js function

    $div_name = 'chatroom_txt';

    // returns recent msgs from chatroom on page load
    $js = 'chatroom_init('.$this->child.',"'.$div_name.'");';

    echo '<div id="'.$div_name.'"></div>';

    echo js_embed($js);

    $form = new XhtmlForm();
    $form->addInput('msg', 'Msg');
    $form->setFocus('msg');
    $form->addHidden('room', $this->child);  // XXX hack
    $form->addSubmit('Send');
    $form->setHandler('chatSubmit');
    echo $form->render();
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
