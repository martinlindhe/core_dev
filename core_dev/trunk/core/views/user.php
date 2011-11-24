<?php

switch ($this->owner) {
case 'tooltip';
    // returns little box of html to show in tooltip userinfo, fetched as a XHR
    // child = user id
    $page->disableDesign();

    $user_id = $this->child;
    $user = User::get($user_id);
    if (!$user)
        die('ECHKKP');

    echo '<h1>Info:'.$user->name.'</h1>';

    echo 'Last active: '.ago($user->time_last_active).'<br/>';
    echo 'Is online: '. ( UserHandler::isOnline($user_id) ? 'YES' : 'NO').'<br/>';
    echo 'User level: '.UserHandler::getUserLevel($user_id).'<br/>';

    $gender_id = UserSetting::get($user_id, 'gender');
    $gender = Setting::getById(USERDATA_OPTIONS, $gender_id);
    echo 'Gender: '.$gender.'<br/>';

    echo 'Otillgänglig för chat?: '.UserSetting::get($user_id, 'chat_off').'<br/>';

    echo 'Presentation: '.UserSetting::get($user_id, 'presentation').'<br/>';

    $pic_id = UserSetting::get($user_id, 'picture');
    if ($pic_id)
    {
        echo 'Profile picture:<br/>';

        $a = new XhtmlComponentA();
        $a->href = getThumbUrl($pic_id, 0, 0);
        $a->content = showThumb($pic_id, 'Profilbild', 150, 150);
        echo $a->render();
    } else {

        $avatar_opt = UserSetting::get($user_id, 'avatar');
        // get pic id from avatar_id
        $avatar_id = UserDataFieldOption::getById($avatar_opt);

        if ($avatar_id) {
            echo 'Avatar:<br/>';

            $a = new XhtmlComponentA();
            $a->href = getThumbUrl($avatar_id, 0, 0);
            $a->content = showThumb($avatar_id, 'Avatar', 150, 150);
            echo $a->render();
        }
    }
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
