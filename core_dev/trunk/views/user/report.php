<?php
/**
 * Self-moderation features, such as "report user", or "report photo"
 */


//TODO: ability to report photos

$session->requireLoggedIn();


switch ($this->owner) {
case 'user':
    // child = user id

    function handleReportUser($p)
    {
        ModerationObject::add(MODERATE_USER, $p['id'], $p['reason']);

        js_redirect('u/profile/'.$p['id']);
    }

    $u = User::get($this->child);
    if (!$u)
        die('WAHH');

    if ($u->id == $session->id)
        die('WEEAWHH');

    echo '<h1>Report user '.$u->name.'</h1>';

    $form = new XhtmlForm();
    $form->addHidden('id', $u->id); //XXX ugly hack
    $form->addTextarea('reason', 'Reason');

    $form->addSubmit('Send');
    $form->setHandler('handleReportUser');
    echo $form->render();
    break;

case 'photo':
    // child = file id

    function handleReportPhoto($p)
    {
        ModerationObject::add(MODERATE_PHOTO, $p['id'], $p['reason']);

        js_redirect('u/photo/show/'.$p['id']);
    }

    $f = File::get($this->child);
d($f);

    echo '<h1>Report photo '.$f->id.'</h1>';

    $form = new XhtmlForm();
    $form->addHidden('id', $f->id); //XXX ugly hack
    $form->addTextarea('reason', 'Reason');

    $form->addSubmit('Send');
    $form->setHandler('handleReportPhoto');
    echo $form->render();
    break;

default:
    echo 'no such view: '.$this->owner;
}

?>
