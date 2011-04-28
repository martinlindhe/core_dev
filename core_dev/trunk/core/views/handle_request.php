<?php

require_once('RegisterHandler.php');

$session->resume();

//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
if ($session->id && $session->ip && ($session->ip != client_ip()) ) {
    $msg = 'ERROR: Client IP changed! Old: '.$session->ip.', current: '.client_ip();
    $error->add($msg);
//die($msg);
    dp($msg);
    $session->end();
//    $session->errorPage();
}

//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
if (!$session->id && !empty($_POST['login_usr']) && isset($_POST['login_pwd']))
{
    $session->login($_POST['login_usr'], $_POST['login_pwd']);
}
//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
else if ($session->id && isset($_GET['logout']))
{
    $session->logout();
}

?>
