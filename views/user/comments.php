<?php
/**
 * Default view for a list of comments
 *
 * INPUT PARAMS:
 *   $this->type   - comment type
 *   $this->owner  - owner object
 */

//STATUS: unfinished

//TODO: pagination? or auto-hide some comments
//TODO: ability to create private comments (tbl flag is there)
//TODO: allow anon comments with Captcha (?)

namespace cd;

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

    $c->store();
    redir($_SERVER['REQUEST_URI']);
}

$list = Comment::getByTypeAndOwner($this->type, $this->owner);

foreach ($list as $c)
{
    $user = User::get($c->creator);
    if ($user)
        echo $user->name.' wrote: ';
    else
        echo 'user id '.$c->creator.' wrote: ';

    echo nl2br($c->msg).'<br/>';

    echo '<span title="'.ago($c->time_created).'">';
    echo sql_datetime($c->time_created);
    echo '</span>';
    echo '<hr/>';
}

if ($session->id)
{
    $form = new XhtmlForm('addcomment');
    $form->addHidden('type', $this->type);
    $form->addHidden('owner', $this->owner);
    $form->addRichedit('comment', t('Write a comment'), '', 300, 80 );
    $form->addSubmit('Save');
    $form->setHandler('handleSubmit');
    echo $form->render();
}

?>
