<?php

//TODO: mark entries as read, see number of unread entries

require_once('Guestbook.php');

if (!$session->id)
    die('XXX gb only for logged in users');


$user_id = $this->owner;

if (!$this->owner)
    $user_id = $session->id;


$user = new User($user_id);

echo '<h1>Guestbook for '.$user->name.'</h1>';

$form = new XhtmlForm('msg');
$form->addHidden('to', $this->owner);
$form->addTextarea('body', 'Body');
$form->addSubmit('Send');
$form->setFocus('body');
$form->onSubmit('return check_gb(this);');
$form->setHandler('gbHandler');


$form->handle(); // to get latest added entry in the following query

$gb = Guestbook::getEntries($user_id);

d( $gb);


if ($user_id == $session->id)
    return;

$header->registerJsFunction(
'function check_gb(frm)'.
'{'.
    'if (!frm.body.value)'.
        'return false;'.
    'return true;'.
'}'
);

function gbHandler($p)
{
    $session = SessionHandler::getInstance();

    if ($session->id == $p['to'])
        return false;

    $x = new Guestbook();
    $x->owner = $p['to'];
    $x->creator = $session->id;
    $x->time_created = sql_datetime( time() );
    $x->body = $p['body'];
    Guestbook::store($x);

    return true;
}

echo '<h1>New guestbook entry to '.$user->name.'</h1>';

echo $form->render();

?>
