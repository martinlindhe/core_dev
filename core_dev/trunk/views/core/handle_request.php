<?php

$session->start();

if ($session->id && $session->ip && ($session->ip != client_ip()) )
{
    // Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
    $msg = 'ERROR: Client IP changed for '.$session->username.', Old: '.$session->ip.', current: '.client_ip();
    $error->add($msg);
    dp($msg);
    $session->end();

//    $session->errorPage();
}
else if ($session->id && $session->getLastActive() < (time() - $session->timeout))
{
    // Check user activity - log out inactive user
    $msg = 'Session timed out for '.$session->username.' after '.(time() - $session->getLastActive()).'s (timeout is '.($session->timeout).'s)';
    $error->add($msg);
    dp($msg);
    $session->end();

    throw new exception ( $msg );

    //$session->showErrorPage();
}
else if ($session->id)
{
    $session->setLastActive();
}
else if (!$session->id && $session->facebook_app_id)
{
    // Handle facebook login
    $session->handleFacebookLogin();
}

?>
