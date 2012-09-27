<?php

namespace cd;

require_once('FeedbackItem.php');

function fbHandler($p)
{
    $session = SessionHandler::getInstance();

    $o = new FeedbackItem();
    $o->subject = $p['subj'];
    $o->body   = $p['body'];
    $o->from = $session->id;
    $o->time_created = sql_datetime( time() );
    FeedbackItem::store($o);

    js_redirect(''); // jump to start page
}

echo '<h2>Send us feedback</h2>';
$x = new XhtmlForm();
$x->addInput('subj', 'Subject');
$x->addTextarea('body', 'Body');
$x->addSubmit('Send');
$x->setHandler('fbHandler');
echo $x->render();

?>
