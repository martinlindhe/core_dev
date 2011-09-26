<?php

//STATUS: very wip, nonfunctional

// TODO: ability to request name change, which will go to admin moderation queue
// TODO: ability to change email (+ require verification of new email)

require_once('ModerationObject.php');

$session->requireLoggedIn();

$view = !$this->owner ? 'default': $this->owner;

switch ($view) {
case 'default':
    echo '<h1>Edit your profile</h1>';
    echo '&raquo; '.ahref('coredev/view/profile_edit/username', 'Change username');
    break;

case 'username':

    function handleSubmit($p)
    {
        $p['new_user'] = trim($p['new_user']);

        $session = SessionHandler::getInstance();

        // dont put empty names or current username on request queue
        if (!$p['new_user'] || $p['new_user'] == $session->username)
            return false;

        //XXX see if username is taken, or is ok according to username stuff

        // put request on queue for admins
        ModerationObject::add(MODERATE_CHANGE_USERNAME, $p['new_user']);

        echo '<div class="good">Your request for username change have been submitted and will be handled soon!</div>';
    }

    echo '<h1>Change username</h1>';

    echo 'We will review your request to change username as soon as possible!<br/><br/>';

    //XXX do user have a pending request for username change??


    // XXXX FIXME: use js validation from register view
    $form = new XhtmlForm('chg_user');
    $form->addInput('new_user', 'New username');

    $form->addSubmit('Save');
    $form->setHandler('handleSubmit');

    echo $form->render();
    break;

default:
    echo 'no such view: '.$view;
}


?>



