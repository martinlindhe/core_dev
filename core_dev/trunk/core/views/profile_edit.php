<?php

//STATUS: wip

// TODO: ability to change email (+ require verification of new email)

require_once('ModerationObject.php');

$session->requireLoggedIn();

$view = !$this->owner ? 'default': $this->owner;

switch ($view) {
case 'default':

    function handleEdit($p)
    {
        $session = SessionHandler::getInstance();

        foreach (UserDataField::getAll() as $f)
        {
            switch ($f->type) {
            case UserDataField::IMAGE:
                if ($p[$f->name]['error'] == UPLOAD_ERR_NO_FILE)
                    continue;

                $fileId = FileHelper::import(USER, $p[$f->name]);
                UserSetting::set($session->id, $f->name, $fileId);
                break;

            default:
                UserSetting::set($session->id, $f->name, $p[$f->name]);
            }
        }

        js_redirect('coredev/view/profile');
    }

    echo '<h1>Edit your profile</h1>';
    echo '<br/>';


    $form = new XhtmlForm();

    $fields = UserDataField::getAll();
    foreach ($fields as $f) {
        switch ($f->type) {
        case UserDataField::RADIO:
            $opts = UserDataFieldOption::getAll($f->id);

            $arr = array();
            foreach ($opts as $o)
                $arr[ $o['settingId'] ] = $o['settingValue'];

            $form->addRadio( $f->name, $f->name, $arr, UserSetting::get($session->id, $f->name));
            break;

        case UserDataField::IMAGE:

            $pic_id = UserSetting::get($session->id, 'picture');
            if ($pic_id) {
                $img = new XhtmlComponentImage();
                $img->src = getThumbUrl($pic_id);
                $form->add($img, 'Existing picture');
            }

            $form->addFile( $f->name, $f->name);
            break;

        case UserDataField::CHECKBOX:
            $form->addCheckbox( $f->name, $f->name, UserSetting::get($session->id, $f->name));
            break;

        default:
            $form->addInput( $f->name, $f->name, UserSetting::get($session->id, $f->name) );
        }
    }

    $form->addSubmit('Save');
    $form->setHandler('handleEdit');

    echo $form->render();

    echo '<br/><br/>';

    echo '&raquo; '.ahref('coredev/view/profile_edit/username', 'Change username').'<br/>';
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
    $form = new XhtmlForm();
    $form->addInput('new_user', 'New username');

    $form->addSubmit('Save');
    $form->setHandler('handleSubmit');

    echo $form->render();
    break;

default:
    echo 'no such view: '.$view;
}


?>



