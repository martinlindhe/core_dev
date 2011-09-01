<?php

if (!$session->id)
    die('XXX profile only for logged in users');

$user_id = $session->id;

if ($this->owner)
    $user_id = $this->owner;


$user = new User($user_id);
//d($user);

echo '<h1>Profile for '.$user->name.'</h1>';

echo 'Last active: '.ago($user->time_last_active).'<br/>';
echo 'Is online: '. ( $user->is_online ? 'YES' : 'NO').'<br/>';


echo '<br/>';

if ($session->id && $user_id != $session->id)
    echo '&raquo; '.ahref('coredev/view/profile_message/'.$user_id, 'Send message').'<br/>';

echo '&raquo; '.ahref('coredev/view/profile_guestbook/'.$user_id, 'Guestbook').'<br/>';

?>
