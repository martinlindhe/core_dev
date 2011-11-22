<?php
/**
 * Default view for a user profile
 */

require_once('Image.php'); // for showThumb()
require_once('Bookmark.php');
require_once('YuiLightbox.php');

$session->requireLoggedIn();

$user_id = $session->id;

if ($this->owner)
    $user_id = $this->owner;

$user = User::get($user_id);

if (!$user)
    die('ECHKKP');

if (Bookmark::exists(BOOKMARK_USERBLOCK, $session->id, $user_id)) {
    echo 'User has blocked you from access';
    return;
}

echo '<h1>Profile for '.$user->name.'</h1>';

echo 'Last active: '.ago($user->time_last_active).'<br/>';
echo 'Is online: '. ( UserHandler::isOnline($user_id) ? 'YES' : 'NO').'<br/>';
echo 'User level: '.UserHandler::getUserLevel($user_id).'<br/>';

$gender_id = UserSetting::get($user_id, 'gender');
$gender = Setting::getById(USERDATA_OPTIONS, $gender_id);
echo 'Gender: '.$gender.'<br/>';


echo 'E-mail: '.UserSetting::get($user_id, 'email').'<br/>';
echo 'Otillgänglig för chat?: '.UserSetting::get($user_id, 'chat_off').'<br/>';

echo 'Presentation: '.UserSetting::get($user_id, 'presentation').'<br/>';

$pic_id = UserSetting::get($user_id, 'picture');
if ($pic_id)
{
    echo 'Profile picture:<br/>';

    // shows the photo
    $a = new XhtmlComponentA();
    $a->href = getThumbUrl($pic_id, 0, 0);
    $a->rel  = 'lightbox';
    $a->content = showThumb($pic_id, 'Profilbild', 150, 150);
    echo $a->render();

    $lb = new YuiLightbox();
    echo $lb->render().'<br/>';
} else {

    $avatar_opt = UserSetting::get($session->id, 'avatar');

    // XXX: get pic id from avatar_id
    $avatar_id = UserDataFieldOption::getById($avatar_opt);

    if ($avatar_id) {
        // shows the photo
        $a = new XhtmlComponentA();
        $a->href = getThumbUrl($avatar_id, 0, 0);
        $a->rel  = 'lightbox';
        $a->content = showThumb($avatar_id, 'Avatar', 150, 150);
        echo $a->render();

        $lb = new YuiLightbox();
        echo $lb->render().'<br/>';
    }
}


echo '<br/>';

if ($session->id && $user_id != $session->id) {
    echo '&raquo; '.ahref('iview/profile_messages/send/'.$user_id, 'Send message').'<br/>';

    if (Bookmark::exists(BOOKMARK_FAVORITEUSER, $user_id, $session->id)) {
        echo '&raquo; '.ahref('iview/bookmark/removeuser/'.$user_id, 'Remove favorite').'<br/>';
    } else {
        echo '&raquo; '.ahref('iview/bookmark/adduser/'.$user_id, 'Add favorite').'<br/>';
    }
    echo '<br/>';

    if (Bookmark::exists(BOOKMARK_USERBLOCK, $user_id, $session->id)) {
        echo '<b>THIS USER IS BLOCKED FROM CONTACTING YOU</b><br/>';
    } else {
        echo '&raquo; '.ahref('iview/block/user/'.$user_id, 'Block user').'<br/>';
    }
    echo '&raquo; '.ahref('iview/report/user/'.$user_id, 'Report user').'<br/>';
}

echo '&raquo; '.ahref('iview/profile_guestbook/'.$user_id, 'Guestbook').'<br/>';
echo '&raquo; '.ahref('iview/album/overview/'.$user_id, 'Photos').'<br/>';

echo '<br/>';

if ($session->id && $user_id == $session->id)
    echo '&raquo; '.ahref('iview/profile_edit', 'Edit profile').'<br/>';

?>
