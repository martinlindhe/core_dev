<?php

//STATUS: wip

// TODO: ability to change email (+ require verification of new email)
// TODO: check minimum allowed password lenght etc when user changes password

namespace cd;

require_once('ModerationObject.php');
require_once('Image.php'); // for getThumbUrl()
require_once('PhotoAlbum.php');
require_once('PersonalStatus.php');

$session->requireLoggedIn();

$view = !$this->owner ? 'default': $this->owner;

switch ($view) {
case 'default':

    function handleEdit($p)
    {
        $session = SessionHandler::getInstance();

        foreach (UserDataField::getAll() as $f)
        {
            if (!empty($p['remove_'.$f->id])) {
                UserSetting::set($session->id, $f->name, 0);
                continue;
            }

            switch ($f->type) {
            case UserDataField::IMAGE:
                if ($p[$f->name]['error'] == UPLOAD_ERR_NO_FILE)
                    continue;

                $album = PhotoAlbum::getProfileAlbumId();

                $fileId = File::importImage(USER, $p[$f->name], $album);
                UserSetting::set($session->id, $f->name, $fileId);
                break;

            default:
                UserSetting::set($session->id, $f->name, $p[$f->name]);
            }
        }

        js_redirect('u/profile');
    }

    echo '<h1>Edit your profile</h1>';
    echo '<br/>';

    $form = new XhtmlForm();

    $fields = UserDataField::getAll();
    foreach ($fields as $f)
    {
        switch ($f->type) {
        case UserDataField::RADIO:
            $opts = UserDataFieldOption::getAll($f->id);

            $arr = array();
            foreach ($opts as $o)
                $arr[ $o['id'] ] = $o['value'];

            $form->addRadio( $f->name, $f->label, $arr, UserSetting::get($session->id, $f->name));
            break;

        case UserDataField::AVATAR:
            $opts = UserDataFieldOption::getAll($f->id);

            $arr = array();
            foreach ($opts as $o) {

                $img = new XhtmlComponentImage();
                $img->src = getThumbUrl($o['value']);
                $arr[ $o['id'] ] = $img->render();
            }

            $form->addRadio( $f->name, $f->label, $arr, UserSetting::get($session->id, $f->name));
            break;


        case UserDataField::IMAGE:
            $pic_id = UserSetting::get($session->id, 'picture');

            if ($pic_id) {
                $img = new XhtmlComponentImage();
                $img->src = getThumbUrl($pic_id);
                $form->add($img, 'Existing picture');

                $form->addCheckbox('remove_'.$f->id, 'Remove photo');
            }

            $form->addFile( $f->name, $f->label);
            break;

        case UserDataField::CHECKBOX:
            $form->addCheckbox( $f->name, $f->label, UserSetting::get($session->id, $f->name));
            break;

        default:
            $form->addInput( $f->name, $f->label, UserSetting::get($session->id, $f->name) );
        }
    }

    $form->addSubmit('Save');
    $form->setHandler('handleEdit');
    echo $form->render();

    echo '<br/><br/>';

    echo '&raquo; '.ahref('u/edit/username', 'Change username').'<br/>';
    echo '&raquo; '.ahref('u/edit/password', 'Change password').'<br/>';

    echo '&raquo; '.ahref('u/block/manage', 'Manage blocked users').'<br/>';
    break;

case 'username':

    function handleEditUsername($p)
    {
        $p['new_user'] = trim($p['new_user']);

        $error = ErrorHandler::getInstance();
        $session = SessionHandler::getInstance();

        // dont put empty names or current username on request queue
        if (!$p['new_user'] || $p['new_user'] == $session->username) {
            $error->add('Useless request');
            return false;
        }

        if (User::getByName($p['new_user'])) {
            $error->add('Username taken');
            return false;
        }

        if (ReservedWord::isReservedUsername($p['new_user'])) {
            $error->add('Username is reserved');
            return false;
        }

        // put request on queue for admins
        ModerationObject::add(MODERATE_CHANGE_USERNAME, $p['new_user']);

        echo '<div class="good">Your request for username change have been submitted and will be handled soon!</div>';
    }

    echo '<h1>Change username</h1>';

    echo 'We will review your request to change username as soon as possible!<br/><br/>';

    //XXX do user have a pending request for username change??

    // XXXX FIXME: use js validation from register view
    $form = new XhtmlForm();
    $form->addInput('new_user', 'New username');

    $form->addSubmit('Save');
    $form->setHandler('handleEditUsername');

    echo $form->render();
    break;

case 'password':

    function handleEditPassword($p)
    {
        $error = ErrorHandler::getInstance();
        $session = SessionHandler::getInstance();

        $u = User::getExact($session->type, $session->id, $session->username, $p['curr_pwd']);
        if (!$u) {
            $error->add('Current password is not correct');
            return false;
        }

        if ($p['new_pwd'] != $p['new_pwd2']) {
            $error->add('passwords dont match');
            return false;
        }

        if (!$p['new_pwd']) {
            $error->add('no password entered');
            return false;
        }

        UserHandler::setPassword($session->id, $p['new_pwd']);

        js_redirect('u/edit');
    }

    echo '<h1>Change password</h1>';

    // XXXX FIXME: use js validation from register view
    $form = new XhtmlForm();
    $form->addPassword('curr_pwd', 'Current password');

    $form->addPassword('new_pwd', 'New password');
    $form->addPassword('new_pwd2', 'New password again');

    $form->addSubmit('Save');
    $form->setHandler('handleEditPassword');

    echo $form->render();
    break;

case 'status':

    function handleEditStatus($p)
    {
        $session = SessionHandler::getInstance();
        if (!$session->id)
            return;

        PersonalStatus::setStatus($session->id, $p['status']);

        js_redirect('u/profile');
    }

    echo '<h1>Change status</h1>';

    $form = new XhtmlForm();
    $form->addInput('status', '');
    $form->addSubmit('Save');
    $form->setHandler('handleEditStatus');
    $form->setFocus('status');
    echo $form->render();
    break;

    break;

default:
    echo 'no such view: '.$view;
}


?>



