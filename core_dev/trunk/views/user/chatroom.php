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
        echo ahref('u/chatroom/show/'.$cr->id, $cr->name).'<br/>';

    break;

case 'update':
    // JSON - returns messages in chatroom (since ts)
    // child = room id

    $page->setMimeType('text/plain');

    if (!$this->child || !is_numeric($this->child))
        die('hey');

    $ts = 0;

    if (isset($_GET['ts']) && is_numeric($_GET['ts'])) {
        $ts = $_GET['ts'];
        $max_age = parse_duration('7d');

        // empty json string on old queries
        if ($ts < microtime(true) - $max_age) {
            echo '[]';
            return;
        }
    }

    $res = ChatMessage::getRecent($this->child, $ts, 25);
    $out = array();
    foreach ($res as $r)
        $out[] = array('name'=>User::get($r->from)->name,'from'=>$r->from,'msg'=>$r->msg,'ts'=>$r->microtime);

    echo json_encode($out);
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

    $div_name = 'chatroom_txt';
    $form_id = 'chatfrm';
    ChatRoomUpdater::init($this->child, $div_name, $form_id);

    $css =
    'width:500px;'.
    'height:300px;'.
    'background-color:#eee;'.
    'overflow:auto;';

    echo '<div id="'.$div_name.'" style="'.$css.'"></div>';

    $form = new XhtmlForm();
    $form->setId($form_id);
    $form->addInput('msg', $session->username.':', '', 445);
    $form->setFocus('msg');
    $form->disableAutocomplete();
    echo $form->render();

    YuiTooltip::init();

echo UserLink::render(27, "martin").' ';
echo UserLink::render(32, "kotte");

    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
