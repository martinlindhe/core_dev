<?php
/**
 * Default view for a user profile
 */

if (!$session->id)
    die('XXX profile only for logged in users');

$user_id = $session->id;

if ($this->owner)
    $user_id = $this->owner;


$user = User::get($user_id);

echo '<h1>Profile for '.$user->name.'</h1>';

echo 'Last active: '.ago($user->time_last_active).'<br/>';
echo 'Is online: '. ( UserHandler::isOnline($user_id) ? 'YES' : 'NO').'<br/>';
echo 'User level: '.UserHandler::getUserLevel($user_id).'<br/>';

echo '<br/>';

if ($session->id && $user_id != $session->id)
    echo '&raquo; '.ahref('coredev/view/profile_message/'.$user_id, 'Send message').'<br/>';

echo '&raquo; '.ahref('coredev/view/profile_guestbook/'.$user_id, 'Guestbook').'<br/>';

?>
