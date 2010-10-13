<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: remember client ip between requests in order to mitigate session hijacking
//TODO: is setActive() method even nessecary with the RequestHandler blacklist???
//FIXME: session timeout verkar inte funka rätt?!?!? vill kunna ha session i 7 dygn den dör efter nån timme iaf

define('USERLEVEL_NORMAL',      0);
define('USERLEVEL_WEBMASTER',   1);
define('USERLEVEL_ADMIN',       2);
define('USERLEVEL_SUPERADMIN',  3);

class SessionHandler extends CoreBase
{
    static $_instance;             ///< singleton

    var $id;
    var $username;
    var $usermode;                 ///< 0=normal user. 1=webmaster, 2=admin, 3=super admin
    var $referer = '';             ///< return to this page after login (if user is browsing a part of the site that is blocked by $this->requireLoggedIn() then logs in)
    var $timeout = 86400;          ///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
    var $online_timeout = 1800;    ///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc

    var $name = 'core_dev_sid';    ///< session cookie name, needs to be unique for multiple projects on same webhost
    var $start_page = '/';         ///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
    var $logged_out_start_page = '/';
    var $error_page = '/error';    ///< redirects the user to this page to show errors

    var $active = true;            ///< is session active (update user last active?)

    var $isWebmaster;              ///< is user webmaster?
    var $isAdmin;                  ///< is user admin?
    var $isSuperAdmin;             ///< is user superadmin?

    private function __construct() { }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setName($s) { $this->name = $s; }
    function setTimeout($n) { $this->timeout = $n; }

    function setActive($b) { $this->active = $b; }

    /**
     * Resumes the session from previous request
     */
    function resume()
    {
        if (!$this->active) return;

        ini_set('session.gc_maxlifetime', $this->timeout);

        session_name($this->name);
        session_start();

        $this->id           = &$_SESSION['id'];    //if id is set, also means that the user is logged in
        $this->username     = &$_SESSION['username'];
        $this->usermode     = &$_SESSION['usermode'];
        $this->isWebmaster  = &$_SESSION['isWebmaster'];
        $this->isAdmin      = &$_SESSION['isAdmin'];
        $this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
        $this->referer      = &$_SESSION['referer'];

        if (empty($_COOKIE[$this->name])) {
            dp('Session expired');
            $this->end();
        }

        if (!$this->id)
            return;

        //sets httponly param to mitigate XSS attacks
        $domain = ''; //XXX read domain name from XmlDocumentHandler->getBaseUrl()
        setcookie($this->name, $_COOKIE[$this->name], time()+$this->timeout, '/', $domain, false, true);

        //Logged in: Check user activity - log out inactive user
        //FIXME: redo this- check lastactive timestamp from db when session is resumed instead
        /*
        if ($this->lastActive < (time()-$this->timeout)) {
            $this->log('Session timed out after '.(time()-$this->session->lastActive).' (timeout is '.($this->session->timeout).')', LOGLEVEL_NOTICE);
            $this->end();
            $error->add('Session timed out');
            $this->showErrorPage();
        }*/

        $this->updateActiveTime();

//        if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) $this->ip = IPv4_to_GeoIP(client_ip()); //FIXME map to $this->auth->ip
    }

    /**
     * Kills the current session, clearing all session variables
     */
    function end()
    {
        $this->id           = 0;
        $this->username     = '';
        $this->usermode     = 0;
        $this->referer      = '';
        $this->isWebmaster  = false;
        $this->isAdmin      = false;
        $this->isSuperAdmin = false;
    }

    /**
     * Sets up a session. Called from the auth class
     *
     * @param $id user id
     * @param $username user name
     */
    function start($id, $username)
    {
        $this->id = $id;
        $this->username = $username;

        $user = new UserHandler($id);
        $this->usermode = $user->getUserLevelByGroup();

        if ($this->usermode >= USERLEVEL_WEBMASTER)  $this->isWebmaster  = true;
        if ($this->usermode >= USERLEVEL_ADMIN)      $this->isAdmin      = true;
        if ($this->usermode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;

        $this->updateLoginTime();

        $db = SqlHandler::getInstance();

        $geoip = IPv4_to_GeoIP(client_ip());
        $db->insert('INSERT INTO tblLogins SET timeCreated=NOW(), userId='.$this->id.', IP='.$geoip.', userAgent="'.$db->escape($_SERVER['HTTP_USER_AGENT']).'"');

        //addEvent(EVENT_USER_LOGIN, 0, $this->id);
    }

    private function updateActiveTime()
    {
        if (!$this->id || !$this->active) return;

        $db = SqlHandler::getInstance();
        $db->update('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$this->id);
    }

    private function updateLoginTime()
    {
        $db = SqlHandler::getInstance();
        $db->update('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$this->id);
    }

    function setLogoutTime()
    {
        $db = SqlHandler::getInstance();
        $db->update('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId='.$this->id);
    }

    /**
     * Locks registered users out from certain pages, such as registration page
     */
    function requireLoggedOut()
    {
        if (!$this->id) return;
        $this->startPage();
    }

    /**
     * Locks unregistered users out from certain pages
     */
    function requireLoggedIn()
    {
        if ($this->id) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires you to be logged in.');

        $this->referer = $_SERVER['REQUEST_URI'];
        $this->showErrorPage();
    }


    /**
     * Locks normal users out from certain pages
     */
    function requireWebmaster()
    {
        if ($this->isWebmaster) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires webmaster rights to view.');
        $this->showErrorPage();
    }

    /**
     * Locks normal users & webmasters out from certain pages
     */
    function requireAdmin()
    {
        if ($this->isAdmin) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires admin rights to view.');
        $this->showErrorPage();
    }

    /**
     * Locks out everyone except for super-admin from certain pages
     */
    function requireSuperAdmin()
    {
        if ($this->isSuperAdmin) return;

        $error = ErrorHandler::getInstance();
        $error->add('The page you requested requires superadmin rights to view.');
        $this->showErrorPage();
    }

    /**
     * Locks out everyone not from localhost (for setup scripts)
     */
    function requireLocalhost()
    {
        if (GeoIP_to_IPv4($this->ip) == '127.0.0.1') return;
        die;
    }

    /**
     * Redirects user to error page
     */
    function showErrorPage()
    {
        $db = SqlHandler::getInstance();

        if ($db->getErrorCount()) {
            echo "DEBUG: session->redirect aborted due to error".ln();
            return;
        }

        js_redirect($this->error_page);
    }


    /**
     * Redirects user to default start page (logged in)
     */
    function showStartPage()
    {
        if (!empty($this->referer))
            js_redirect($this->referer);

        js_redirect($this->start_page);
    }

    /**
     * Redirects user to default start page (logged out)
     */
    function showLoggedOutStartPage()
    {
       js_redirect($this->logged_out_start_page);
    }

}

?>
