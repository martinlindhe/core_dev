<?php
/**
 * Default view for a user profile
 */

require_once('Image.php'); // for showThumb()
require_once('Bookmark.php');

$session->requireLoggedIn();

$user_id = $session->id;

if ($this->owner)
    $user_id = $this->owner;

$user = User::get($user_id);

if (!$user)
    die('ECHKKP');

if (Bookmark::exists($session->id, BOOKMARK_USERBLOCK, $user_id)) {
    echo 'User has blocked you from access';
    return;
}

echo '<h1>Profile for '.$user->name.'</h1>';

echo 'Last active: '.ago($user->time_last_active).'<br/>';
echo 'Is online: '. ( UserHandler::isOnline($user_id) ? 'YES' : 'NO').'<br/>';
echo 'User level: '.UserHandler::getUserLevel($user_id).'<br/>';

$gender_id = UserSetting::get($user_id, 'gender');
$gender = Setting::getById($gender_id);
echo 'Gender: '.$gender.'<br/>';


echo 'E-mail: '.UserSetting::get($user_id, 'email').'<br/>';
echo 'Want ads?: '.UserSetting::get($user_id, 'want_ads').'<br/>';

echo 'Presentation: '.UserSetting::get($user_id, 'presentation').'<br/>';

$pic_id = UserSetting::get($user_id, 'picture');
if ($pic_id)
    echo 'Profile picture: '.showThumb($pic_id, 'Profilbild', 100, 100).'<br/>';



echo '<br/>';

if ($session->id && $user_id != $session->id) {
    echo '&raquo; '.ahref('iview/profile_messages/send/'.$user_id, 'Send message').'<br/>';

    if (Bookmark::exists($user_id, BOOKMARK_FAVORITEUSER, $session->id)) {
        echo '&raquo; '.ahref('iview/bookmark/removeuser/'.$user_id, 'Remove favorite').'<br/>';
    } else {
        echo '&raquo; '.ahref('iview/bookmark/adduser/'.$user_id, 'Add favorite').'<br/>';
    }
    echo '<br/>';

    if (Bookmark::exists($user_id, BOOKMARK_USERBLOCK, $session->id)) {
        echo '<b>THIS USER IS BLOCKED FROM CONTACTING YOU</b><br/>';
    } else {
        echo '&raquo; '.ahref('iview/block/user/'.$user_id, 'Block user').'<br/>';
    }
    echo '&raquo; '.ahref('iview/report/user/'.$user_id, 'Report user').'<br/>';
}

echo '&raquo; '.ahref('iview/profile_guestbook/'.$user_id, 'Guestbook').'<br/>';
echo '&raquo; '.ahref('iview/albums/overview/'.$user_id, 'Photos').'<br/>';

echo '<br/>';

if ($session->id && $user_id == $session->id)
    echo '&raquo; '.ahref('iview/profile_edit', 'Edit profile').'<br/>';

?>
