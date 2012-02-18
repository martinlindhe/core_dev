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

if (UserHandler::isOnline($user_id)) {
    echo 'Last active '.ago($user->time_last_active).'<br/>';
    echo 'Otillgänglig för chat?: '.UserSetting::get($user_id, 'chat_off').'<br/>';
} else {
    echo 'Offline<br/>';
}

echo 'User level: '.UserGroupHandler::getUserLevel($user_id).'<br/>';

$gender_id = UserSetting::get($user_id, 'gender');
$gender = Setting::getById(USERDATA_OPTION, $gender_id);
echo 'Gender: '.$gender.'<br/>';


echo 'E-mail: '.UserSetting::get($user_id, 'email').'<br/>';
$pres = UserSetting::get($user_id, 'presentation');
if ($pres)
    echo 'Presentation: '.$pres.'<br/>';

$pic_id = UserSetting::get($user_id, 'picture');
if ($pic_id)
{
    echo 'Profile picture:<br/>';

    $a = new XhtmlComponentA();
    $a->href = getThumbUrl($pic_id, 0, 0);
    $a->rel  = 'lightbox';
    $a->content = showThumb($pic_id, 'Profilbild', 150, 150);
    echo $a->render();

    $lb = new YuiLightbox();
    echo $lb->render().'<br/>';
} else {

    $avatar_opt = UserSetting::get($user_id, 'avatar');
    // get pic id from avatar_id
    $avatar_id = UserDataFieldOption::getById($avatar_opt);

    if ($avatar_id) {
        echo 'Avatar:<br/>';

        $a = new XhtmlComponentA();
        $a->href = getThumbUrl($avatar_id, 0, 0);
        $a->rel  = 'lightbox';
        $a->content = showThumb($avatar_id, 'Avatar', 150, 150);
        echo $a->render();

        $lb = new YuiLightbox();
        echo $lb->render().'<br/>';
    }
}


$pres_id = UserSetting::get($user_id, 'video_pres');
if ($pres_id)
{
    echo 'Presentation:<br/>';

    $pres_div = 'pres_div'.mt_rand();

// flashvars:      'image':  '/thumbs/bunny.jpg'   XXX FOR THUMBNAIL!!!

    // XXX JWPlayer workaround using htaccess: http://www.longtailvideo.com/support/forums/jw-player/setup-issues-and-embedding/25111/jwplayer-thinks-flv-file-is-a-playlist-fails-to-play-it
    $flashvars  = array('file' => '/video/'.$pres_id.'.flv'); // , 'autostart' => 'true' );
    $params     = array('allowfullscreen' => 'false', 'allowscriptaccess' => 'always' );
    //$attributes = array('id' => 'player1', 'name' => 'player1' );

    echo js_swfobject('/jwplayer.swf', $pres_div, 250, 200, $flashvars, $params); // , $attributes);
    
    echo '<div id="'.$pres_div.'"></div>';
}


echo '<br/>';

if ($session->id && $user_id != $session->id) {
    echo '&raquo; '.ahref('u/messages/send/'.$user_id, 'Send message').'<br/>';

    if (Bookmark::exists(BOOKMARK_FAVORITEUSER, $user_id, $session->id)) {
        echo '&raquo; '.ahref('u/bookmark/removeuser/'.$user_id, 'Remove favorite').'<br/>';
    } else {
        echo '&raquo; '.ahref('u/bookmark/adduser/'.$user_id, 'Add favorite').'<br/>';
    }
    echo '<br/>';

    if (Bookmark::exists(BOOKMARK_USERBLOCK, $user_id, $session->id)) {
        echo '<b>THIS USER IS BLOCKED FROM CONTACTING YOU</b><br/>';
    } else {
        echo '&raquo; '.ahref('u/block/user/'.$user_id, 'Block user').'<br/>';
    }
    echo '&raquo; '.ahref('u/report/user/'.$user_id, 'Report user').'<br/>';
}

echo '&raquo; '.ahref('u/guestbook/'.$user_id, 'Guestbook').'<br/>';
echo '&raquo; '.ahref('u/album/overview/'.$user_id, 'Photos').'<br/>';

echo '<br/>';

if ($session->id && $user_id == $session->id)
    echo '&raquo; '.ahref('u/edit', 'Edit profile').'<br/>';

?>
