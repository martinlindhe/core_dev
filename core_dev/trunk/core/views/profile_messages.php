<?php
/**
 * Default view for User's Private Message Inbox and Outbox
 */

//TODO: ability to view 1 full msg (there also see previous conversation with that person)
//TODO: mark seen msg as "READ"
//TODO: ability to mark seen msg as "UNREAD" again
//TODO: ability to mark a message as "DELETED". really useful? will require 2 copies of each msg, or else if user 1 deletes the msg, user2 also wont see it

require_once('Message.php');

require_once('YuiDatatable.php');

$session->requireLoggedIn();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':

    echo '<h1>Private messages</h1>';

    //echo '<h2>Inbox</h2>';
    $list = Message::getInbox($session->id);
    //d($list);

    $dt = new YuiDatatable();
    $dt->setCaption('Inbox');
    $dt->addColumn('from',         'From');    /// XXXX show username, show link to user page
    $dt->addColumn('time_sent',    'Sent');
    $dt->addColumn('body',         'Msg');
    $dt->setSortOrder('time_sent', 'desc');
    $dt->setDataList( $list );
    echo $dt->render();


    //echo '<h2>Outbox</h2>';
    $list = Message::getOutbox($session->id);
    //d($list);

    $dt = new YuiDatatable();
    $dt->setCaption('Outbox');
    $dt->addColumn('to',           'To');    /// XXXX show username, show link to user page
    $dt->addColumn('time_sent',    'Sent');
    $dt->addColumn('body',         'Msg');
    $dt->setSortOrder('time_sent', 'desc');
    $dt->setDataList( $list );
    echo $dt->render();
    break;

case 'send':
    // child = send to user id

    function msgSubmit($p)
    {
        $session = SessionHandler::getInstance();

        $m = new Message();
        $m->to = $p['to'];
        $m->from = $session->id;
        $m->body = $p['msg'];
        $m->time_sent = sql_datetime( time() );
        Message::store($m);

        js_redirect('coredev/view/profile_messages');
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
