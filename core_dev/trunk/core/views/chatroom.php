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
        echo ahref('iview/chatroom/show/'.$cr->id, $cr->name).'<br/>';

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
    $res = ChatMessage::getRecent($this->child, $ts, 25);

    $page->setMimeType('text/plain');
    echo json_encode($res);
    break;

case 'send':
    // XHR - writes a message to chatroom
    // child = room id
    // $_GET['m'] = message
    if (!$this->child || !is_numeric($this->child) || empty($_GET['m']))
        die('hey');

    $cr = ChatRoom::get($this->child);
    if ($cr->locked_by)
        die('hey3');

    $m = new ChatMessage();
    $m->room = $this->child;
    $m->from = $session->id;
    $m->msg  = $_GET['m'];
    $m->microtime = microtime(true);
    ChatMessage::store($m);

    $page->setMimeType('text/plain');
    echo 'OK';
    break;

case 'show':
    // child = room id

    $cr = ChatRoom::get($this->child);

    echo '<h2>Chat in '.$cr->name.'</h2>';

    if ($cr->locked_by) {
        echo 'The chatroom is locked!';
        return;
    }

    ChatRoomUpdater::init(); // registers the chatroom_init(), chatroom_send() js functions

    $div_name = 'chatroom_txt';

    // returns recent msgs from chatroom on page load
    $js = 'chatroom_init('.$this->child.',"'.$div_name.'");';

    $css =
    'width:500px;'.
    'height:300px;'.
    'background-color:#eee;'.
    'overflow:auto;';

    echo '<div id="'.$div_name.'" style="'.$css.'"></div>';

    echo js_embed($js);

    $form = new XhtmlForm();
    $form->addInput('msg', 'Msg');
    $form->setFocus('msg');
    $form->addSubmit('Send');

    $form->onSubmit("return chatroom_send(this,".$this->child.",'".$div_name."');");

    echo $form->render();
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
