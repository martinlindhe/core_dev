<?php
/**
 * Default view for User's Private Message Inbox and Outbox
 */

//TODO: ability to view 1 full msg (there also see previous conversation with that person)
//TODO: mark seen msg as "READ"
//TODO: ability to mark seen msg as "UNREAD" again
//TODO: ability to mark a message as "DELETED". really useful? will require 2 copies of each msg, or else if user 1 deletes the msg, user2 also wont see it

namespace cd;

require_once('Message.php');
require_once('Bookmark.php');
require_once('YuiDatatable.php');

$session->requireLoggedIn();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'inbox':

    echo '<h1>Message inbox</h1>';
    $list = Message::getInbox($session->id);
    //d($list);
    echo ahref('u/messages/outbox', 'Show outbox').'<br/>';
    echo '<br/>';

    foreach ($list as $msg)
    {
//        $from = User::get($msg->from);

        echo 'From '.UserLink::render($msg->from).', '.ago($msg->time_sent).'<br/>';
//        echo 'Read: '.ago($msg->time_read).'<br/>';  // XXX FIXME mark msgs as read!!!
        switch ($msg->type) {
        case PRIV_MSG:
            echo nl2br($msg->body).'<br/>';
            break;
        case RECORDING_MSG:
            echo embed_flv($msg->body).'<br/>';
            break;
        default:
            throw new \Exception ('eh');
        }
        echo ahref('videomsg/send/'.$msg->from, 'Reply with video').'<br/>';
        echo ahref('u/messages/send/'.$msg->from, 'Reply with text').'<br/>';
        echo '<hr/>';
    }

    break;

case 'outbox':
    echo '<h1>Message outbox</h1>';
    $list = Message::getOutbox($session->id);
    //d($list);
    echo ahref('u/messages/inbox', 'Show inbox').'<br/>';
    echo '<br/>';

    foreach ($list as $msg)
    {
        echo 'To '.UserLink::render($msg->to).', '.ago($msg->time_sent).'<br/>';
        switch ($msg->type) {
        case PRIV_MSG:
            echo nl2br($msg->body).'<br/>';
            break;
        case RECORDING_MSG:
            echo 'VIDEO MSG!!!<br/>';
            echo embed_flv($msg->body).'<br/>';
            break;
        default:
            throw new \Exception ('eh');
        }
        echo '<hr/>';
    }
    break;

case 'send':
    // child = send to user id
    if (Bookmark::exists(BOOKMARK_USERBLOCK, $session->id, $this->child)) {
        echo 'User has blocked you from access';
        return;
    }

    function msgSubmit($p)
    {
        Message::send($p['to'], $p['msg']);
        js_redirect('u/messages/inbox');
    }

    $user = User::get($this->child);

    echo '<h2>Send a message to '.$user->name.'</h2>';

    $form = new XhtmlForm();
    $form->addTextarea('msg', 'Msg');
    $form->addHidden('to', $this->child);
    $form->addSubmit('Send');
    $form->setHandler('msgSubmit');
    $form->setFocus('msg');
    echo $form->render();

    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
