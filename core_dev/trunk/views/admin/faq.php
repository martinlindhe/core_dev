<?php

require_once('FaqItem.php');
require_once('YuiDatatable.php');

$session->requireAdmin();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':
    echo '<h2>FAQ items</h2>';
    $list = FaqItem::getAll();

    $dt = new YuiDatatable();
    $dt->addColumn('id',     'Question', 'link', 'a/faq/edit/', 'question');
    $dt->addColumn('time_created', 'Created');
    $dt->addColumn('creator',    'Creator',       'link', 'u/profile/' );
    $dt->setDataSource( $list );
    echo $dt->render();
    echo '<br/>';
    echo '&raquo; '.ahref('a/faq/add', 'Add new FAQ');
    break;

case 'add':

    function createHandler($p)
    {
        $session = SessionHandler::getInstance();

        $o = new FaqItem();
        $o->question = $p['q'];
        $o->answer   = $p['a'];
        $o->creator = $session->id;
        $o->time_created = sql_datetime( time() );
        FaqItem::store($o);

        js_redirect('a/faq');
    }

    echo '<h2>Create new FAQ</h2>';

    $x = new XhtmlForm();
    $x->addInput('q', 'Question');
    $x->addTextarea('a', 'Answer');
    $x->addSubmit('Create');
    $x->setHandler('createHandler');
    echo $x->render();
    break;

case 'edit':
    // child = tblFAQ.id
    function editHandler($p)
    {
        $session = SessionHandler::getInstance();

        $o = new FaqItem();
        $o->id       = $p['id'];
        $o->question = $p['q'];
        $o->answer   = $p['a'];
        $o->creator = $session->id;
        $o->time_created = sql_datetime( time() );
        FaqItem::store($o);

        js_redirect('a/faq');
    }

    echo '<h2>Edit FAQ</h2>';

    $faq = FaqItem::get($this->child);

    $x = new XhtmlForm();
    $x->addHidden('id', $this->child);
    $x->addInput('q', 'Question', $faq->question);
    $x->addTextarea('a', 'Answer', $faq->answer);
    $x->addSubmit('Save');
    $x->setHandler('editHandler');
    echo $x->render();
    echo '<br/>';
    echo '&raquo; '.ahref('a/faq/delete/'.$this->child, 'Delete FAQ entry');
    break;

case 'delete':
    // child = tblFAQ.id
    if (confirmed('Are you sure you want to delete this FAQ entry?')) {
        FaqItem::remove($this->child);
        js_redirect('a/faq');
    }
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
