<?php
/**
 * Default view for a list of comments
 */

//STATUS: unfinished

//TODO: integrate captcha from Comments_borked.php
//TODO: pagination? or auto-hide some comments


// INPUT PARAMS:
//   $this->type   - comment type
//   $this->owner  - owner object

require_once('User.php');

function handleSubmit($p)
{
    $session = SessionHandler::getInstance();
    $error = ErrorHandler::getInstance();

    if (empty($p['comment']))
        return false;

    if (!$session->id) {
        $error->add('Unauthorized submit');
        return false;
    }

    $c = new Comment();
    $c->type         = $p['type'];
    $c->msg          = $p['comment'];
    $c->private      = 0;
    $c->time_created = sql_datetime( time() );
    $c->owner        = $p['owner'];
    $c->creator      = $session->id;
    $c->creator_ip   = client_ip();

    Comment::store($c);
}

if ($session->id)
{
    $form = new XhtmlForm('addcomment');
    $form->addHidden('type', $this->type);
    $form->addHidden('owner', $this->owner);
    $form->addRichedit('comment', t('Write a comment'), '', 300, 80 );

    $form->addSubmit('Save');
    $form->setHandler('handleSubmit');
}

$form->handle();  // force form processing so following Comment::get() is current

$list = Comment::get($this->type, $this->owner);

foreach ($list as $c)
{
    $user = new User($c->creator);
    echo $user->render().' wrote: ';

    echo nl2br($c->msg).'<br/>';

    echo ago($c->time_created); //XXX snygga till
    echo '<hr/>';
}

echo $form->render();

?>
