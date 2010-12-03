<?php

require_once('UserList.php');
require_once('RegisterHandler.php');

//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
if (isset($_GET['logout']))
    $session->logout();

//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
if (!$session->id && !empty($_POST['login_usr']) && isset($_POST['login_pwd']))
    $session->login($_POST['login_usr'], $_POST['login_pwd']);

//Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
if (!$session->id && $session->allow_registrations && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']))
{
    $reg = RegisterHandler::getInstance();

    if (!$reg->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']))
        return;

    $session->login($_POST['register_usr'], $_POST['register_pwd']);
}

//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
if ($session->id && $session->ip && ($session->ip != client_ip()) ) {
    $msg = 'ERROR: Client IP changed! Old: '.$session->ip.', current: '.client_ip();
    $error->add($msg);
//die($msg);
    dp($msg);
    $session->end();
//    $session->errorPage();
}

?>
