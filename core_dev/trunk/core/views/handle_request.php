<?php

//Logged in: Check for a logout request. Send GET parameter 'logout' to any page to log out
if (isset($_GET['logout']))
    $session->logout();

//Check for login request, POST to any page with 'login_usr' & 'login_pwd' variables set to log in
if (!$session->id && !empty($_POST['login_usr']) && isset($_POST['login_pwd']))
    $session->login($_POST['login_usr'], $_POST['login_pwd']);

//Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
if (!$session->id && $session->allow_registrations && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2'])) {

    $userlist = new UserList();

    if (!$userlist->getCount()) {
        $minlen_username = 3;
        $minlen_password = 4;

        $username = trim($_POST['register_usr']);
        $pwd      = trim($_POST['register_pwd']);

        if (strlen($username) < $minlen_username) {
            $error->add('Username must be at least '.$minlen_username.' characters long');
            return;
        }

        if (strlen($pwd) < $minlen_password) {
            $error->add('Password must be at least '.$minlen_password.' characters long');
            return;
        }

        if ($pwd != $_POST['register_pwd2']) {
            $error->add('Passwords dont match');
            return;
        }

        $user = new User();
        if ($user->loadByName($username)) {
            $error->add('Username taken');
            return;
        }

        $user->create($username);
        if (!$user->getId()) {
            $error->add('Failed to create user');
            return;
        }

        $user->setPassword($pwd);
        $session->login($username, $pwd);
    }
}

//Logged in: Check if client ip has changed since last request, if so - log user out to avoid session hijacking
if ($session->id && $session->check_ip && $session->ip && ($session->ip != client_ip()) ) {
    $msg = t('Client IP changed.').'Client IP changed! Old IP: '.$session->ip.', current: '.client_ip();
    $error->add($msg);
//die($msg);
    dp($msg);
    $session->end();
//    $session->errorPage();
}

?>
