<?php
// XXX implement handling of feedback!!!

require_once('FeedbackItem.php');
require_once('YuiDatatable.php');

$session->requireAdmin();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':
    echo '<h1>User feedback</h1>';
    $list = FeedbackItem::getUnanswered();
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
    $fb = FeedbackItem::get($this->child);

    d($fb);
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
