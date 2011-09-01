<?php

if (!$session->id)
    die('XXX profile only for logged in users');

if ($this->owner)
    throw new Exception ('fixme show profile for id '.$this->owner);

$user_id = $session->id;


echo '<h1>User profile</h1>';

$user = new User($user_id);

d($user);

echo '&raquo; '.ahref('coredev/view/profile_guestbook/'.$user_id, 'Guestbook').'<br/>';

?>
