<?php

//TODO: mark entries as read, see number of unread entries

require_once('Guestbook.php');

require_once('YuiDatatable.php');

if (!$session->id)
    die('XXX gb only for logged in users');


$user_id = $this->owner;

if (!$this->owner)
    $user_id = $session->id;


$user = User::get($user_id);
if (!$user)
    die('ECK');

if (Bookmark::exists($session->id, BOOKMARK_USERBLOCK, $user_id)) {
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
$dt->setDataList( $list );
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
