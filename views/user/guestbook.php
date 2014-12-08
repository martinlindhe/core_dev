<?php

//TODO: mark entries as read, see number of unread entries

namespace cd;

if (!$session->id)
    die('XXX gb only for logged in users');


$user_id = $this->owner;

if (!$this->owner)
    $user_id = $session->id;


$user = User::get($user_id);
if (!$user)
    die('ECK');

if (Bookmark::exists(BOOKMARK_USERBLOCK, $session->id, $user_id)) {
    echo 'User has blocked you from access';
    return;
}

echo '<h1>Guestbook for '.$user->name.'</h1>';

$form = new XhtmlForm('msg');
$form->addHidden('to', $this->owner);
$form->addTextarea('body', 'Body');
$form->addSubmit('Send');
$form->setFocus('body');
$form->onSubmit('return check_gb(this);');
$form->setHandler('gbHandler');

$form->handle(); // to get latest added entry in the following query



$list = Guestbook::getEntries($user_id);

$dt = new YuiDatatable();
$dt->addColumn('creator',         'Written by');    /// XXXX show username, show link to user page
$dt->addColumn('time_created',    'When');
$dt->addColumn('body',            'Msg');
$dt->setSortOrder('time_created', 'desc');
$dt->setDataSource( $list );
echo $dt->render();


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

    $gb = new Guestbook();
    $gb->owner = $p['to'];
    $gb->creator = $session->id;
    $gb->time_created = sql_datetime( time() );
    $gb->body = $p['body'];
    $gb->store();

    return true;
}

echo '<h1>New guestbook entry to '.$user->name.'</h1>';

echo $form->render();

?>
