<?php
/**
 * $Id$
 *
 * Default authentication class. Uses core_dev's own tblUsers
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//STATUS: drop all code, refactor into AuthHandler.php

//TODO: handleForgotPassword(): use client_smtp.php instead!

require_once('auth_base.php');
require_once('design_auth.php');        //default functions for auth xhtml forms
require_once('class.Users.php');

require_once('atom_events.php');        //for event logging
require_once('atom_blocks.php');        //for isBlocked()
require_once('atom_activation.php');    //for generateActivationCode()

class auth_default extends auth_base
{
    var $driver = 'default';

    var $error = '';    ///< contains last error message, if any

    var $sha1_key = 'rpxp8xFDSGsdfgds5tgddgsDh9tkeWljo';    ///< used to further encode sha1 passwords, to make rainbow table attacks harder

    var $allow_login = true;                ///< set to false to only let superadmins log in to the site
    var $allow_registration = true;        ///< set to false to disallow the possibility to register new users. will be disabled if login is disabled
    var $mail_activate = false;            ///< does account registration require email activation?
    var $mail_error = false;                ///< will be set to true if there was problems sending out email

    var $activation_sent = false;        ///< internal. true if mail activation has been sent
    var $resetpwd_sent = false;            ///< internal. true if mail for password reset has been sent

    var $check_ip = true;                ///< client will be logged out if client ip is changed during the session
    var $ip = 0;                        ///< IP of user

    function __construct($conf = array())
    {
        if (isset($conf['sha1_key'])) $this->sha1_key = $conf['sha1_key'];
        if (isset($conf['allow_login'])) $this->allow_login = $conf['allow_login'];
        if (isset($conf['allow_registration'])) $this->allow_registration = $conf['allow_registration'];
        if (isset($conf['mail_activate'])) $this->mail_activate = $conf['mail_activate'];

        if (isset($conf['check_ip'])) $this->check_ip = $conf['check_ip'];

        if (!isset($_SESSION['user_agent'])) $_SESSION['user_agent'] = '';

        $this->ip = &$_SESSION['ip'];
        $this->user_agent = &$_SESSION['user_agent'];

        if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) $this->ip = IPv4_to_GeoIP(client_ip());
    }



}
?>
