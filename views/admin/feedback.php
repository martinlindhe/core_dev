<?php

namespace cd;

$session->requireAdmin();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':
    echo '<h1>User feedback</h1>';
    $list = Feedback::getUnanswered();
//    d($list);

    $dt = new YuiDatatable();
    $dt->addColumn('id',     '#', 'link', 'a/feedback/handle/', 'subject');
    $dt->addColumn('time_created', 'Created');
    $dt->addColumn('from',    'From',       'link', 'u/profile/' );
    $dt->setDataSource( $list );
    echo $dt->render();
    break;

case 'handle':
    // child = tblFeedback.id

    function fbHandle($p)
    {
        $msg_id = Message::send($p['to'], $p['msg']);
        Feedback::markHandled($p['owner'], $msg_id);
        js_redirect('a/feedback/default');
    }

    $fb = Feedback::get($this->child);
    if (!$fb)
        die('Eppp');

    if ($fb->type == USER) {
        $from = User::get($fb->from);
        echo '<h2>User feedback from '.$fb->name.'</h2>';
    }

    echo 'Subject: '.$fb->subject.'<br/>';
    if ($fb->body)
        echo 'Message: '.nl2br($fb->body);
    echo '<br/>';

    if ($fb->type == USER) {
        $msg = "In response to your feedback:\n\n".$fb->body;

        $frm = new XhtmlForm();
        $frm->addHidden('owner', $this->child);
        $frm->addHidden('to', $fb->from);
        $frm->addTextarea('msg', 'Reply', $msg);
        $frm->addSubmit('Send');
        $frm->setHandler('fbHandle');
        echo $frm->render();
    }


    echo '<br/>';
    echo ahref('a/feedback/markhandled/'.$this->child, 'Mark as handled');
    break;

case 'markhandled':
    // child = tblFeedback.id
    Feedback::markHandled($this->child);
    js_redirect('a/feedback/default');
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
