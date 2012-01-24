<?php

require_once('BlogEntry.php');

$session->requireSuperAdmin();

switch ($this->owner) {
case 'overview':
    echo '<h1>Blogs overview</h1>';

    echo '&raquo; '.ahref('a/blogs/new', 'Write a new blog post');
    break;

case 'new':
    function createHandler($p)
    {
        $session = SessionHandler::getInstance();

        $o = new BlogEntry();
        $o->owner = $session->id;
        $o->subject = trim($p['subject']);
        $o->body = trim($p['body']);
        $o->time_created = sql_datetime( time() );

        BlogEntry::store($o);

        js_redirect('a/blogs/overview');
    }

    echo '<h1>Write a new blog</h1>';

    $x = new XhtmlForm();
    $x->addInput('subject', 'Subject');
    $x->addRichedit('body', 'Body');
    $x->addSubmit('Create');
    $x->setHandler('createHandler');
    echo $x->render();
    break;


    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
