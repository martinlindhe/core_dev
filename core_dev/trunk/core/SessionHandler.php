<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: drop session_default.php, session_base.php


class SessionHandler extends CoreBase
{
    static $_instance; ///< singleton class
    var $id;
    var $username;
    var $usermode;              ///< 0=normal user. 1=webmaster, 2=admin, 3=super admin
    var $referer = '';          ///< return to this page after login (if user is browsing a part of the site that is blocked by $this->requireLoggedIn() then logs in)
    var $timeout = 86400;       ///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
    var $online_timeout = 1800;    ///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc

    var $name = 'core_dev_sid'; ///< session cookie name, needs to be unique for multiple projects on same webhost
    var $start_page = 'index.php'; ///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
    var $logged_out_start_page = 'index.php';
    var $error_page = 'error.php'; ///< redirects the user to this page to show errors

    var $isWebmaster;           ///< is user webmaster?
    var $isAdmin;               ///< is user admin?
    var $isSuperAdmin;          ///< is user superadmin?

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setName($s) { $this->name = $s; }
    function setTimeout($n) { $this->timeout = $n; }

    function init()
    {
        //ini_set('session.gc_probability', 1);
        //ini_set('session.gc_divisor', 1);

        //disable garbage collector to work around ubuntu/debian bug, see:
        //   http://forum.kohanaphp.com/comments.php?DiscussionID=565
        ini_set('session.gc_probability', 0);

        ini_set('session.gc_maxlifetime', $this->timeout);

        session_name($this->name);
        session_start();
//XXX fixa bort all denna skit (?)
        if (!isset($_SESSION['started']) || !$_SESSION['started']) $_SESSION['started'] = time();
//        if (!isset($_SESSION['error'])) $_SESSION['error'] = '';
//        if (!isset($_SESSION['ip'])) $_SESSION['ip'] = 0;
        if (!isset($_SESSION['id'])) $_SESSION['id'] = 0;
        if (!isset($_SESSION['username'])) $_SESSION['username'] = '';
        if (!isset($_SESSION['usermode'])) $_SESSION['usermode'] = 0;
//        if (!isset($_SESSION['lastActive'])) $_SESSION['lastActive'] = 0;
        if (!isset($_SESSION['isWebmaster'])) $_SESSION['isWebmaster'] = 0;
        if (!isset($_SESSION['isAdmin'])) $_SESSION['isAdmin'] = 0;
        if (!isset($_SESSION['isSuperAdmin'])) $_SESSION['isSuperAdmin'] = 0;
//        if (!isset($_SESSION['theme'])) $_SESSION['theme'] = $this->default_theme;
        if (!isset($_SESSION['referer'])) $_SESSION['referer'] = '';

        $this->started = &$_SESSION['started'];
//        $this->error = &$_SESSION['error'];
//        $this->ip = &$_SESSION['ip'];
        $this->id = &$_SESSION['id'];    //if id is set, also means that the user is logged in
        $this->username = &$_SESSION['username'];
        $this->usermode = &$_SESSION['usermode'];
//        $this->lastActive = &$_SESSION['lastActive'];
        $this->isWebmaster = &$_SESSION['isWebmaster'];
        $this->isAdmin = &$_SESSION['isAdmin'];
        $this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
//        $this->theme = &$_SESSION['theme'];
        $this->referer = &$_SESSION['referer'];

//        if (!$this->ip && !empty($_SERVER['REMOTE_ADDR'])) $this->ip = IPv4_to_GeoIP(client_ip()); //FIXME map to $this->auth->ip

    }

    /**
     * Kills the current session, clearing all session variables
     */
    function end()
    {
        $this->started = 0;
        $this->id = 0;
        $this->username = '';
        $this->usermode = 0;
//        $this->ip = 0;
        $this->isWebmaster  = false;
        $this->isAdmin      = false;
        $this->isSuperAdmin = false;
        //$this->theme = $this->default_theme;
        $this->referer = '';
    }

    /**
     * Sets up a session. Called from the auth class
     *
     * @param $id user id
     * @param $username user name
     * @param $usermode user mode
     */
    function start($id, $username, $usermode)
    {
        global $config;
        $this->id = $id;
        $this->username = $username;
        $this->usermode = $usermode;
        //$this->lastActive = time();

        if ($this->usermode >= USERLEVEL_WEBMASTER) $this->isWebmaster = true;
        if ($this->usermode >= USERLEVEL_ADMIN) $this->isAdmin = true;
        if ($this->usermode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;

        $this->updateLoginTime();

        //FIXME: move the sql somehwere else
        global $db;
        $geoip = IPv4_to_GeoIP(client_ip());
        $db->insert('INSERT INTO tblLogins SET timeCreated=NOW(), userId='.$this->id.', IP='.$geoip.', userAgent="'.$db->escape($_SERVER['HTTP_USER_AGENT']).'"');

        //addEvent(EVENT_USER_LOGIN, 0, $this->id);

        dp($this->username.' logged in');
    }

    private function updateActiveTime()
    {
        global $db;
        $db->update('UPDATE tblUsers SET timeLastActive=NOW() WHERE userId='.$this->id);
    }

    private function updateLoginTime()
    {
        global $db;
        $db->update('UPDATE tblUsers SET timeLastLogin=NOW(), timeLastActive=NOW() WHERE userId='.$this->id);
    }

    function setLogoutTime()
    {
        global $db;
        $db->update('UPDATE tblUsers SET timeLastLogout=NOW() WHERE userId='.$this->id);
    }

    /**
     * Handles session events, such as idle timeout check. called from the constructor
     */
    function handleEvents()
    {
        //force session handling to be skipped to disallow automatic requests from keeping a user "logged in"
        if (!empty($config['no_session']) || !$this->id) return;

        //Logged in: Check user activity - log out inactive user
        //FIXME: redo this- check lastactive timestamp from db when session is resumed instead
        /*
        if ($this->lastActive < (time()-$this->timeout)) {
            $this->log('Session timed out after '.(time()-$this->session->lastActive).' (timeout is '.($this->session->timeout).')', LOGLEVEL_NOTICE);
            $this->end();
            $this->setError('Session timed out');
            $this->showErrorPage();
        }*/

        if (!$this->id) return;

        $this->updateActiveTime();
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
        global $config;
        if ($this->id) return;
        $this->setError( t('The page you requested requires you to be logged in.') );
        if (empty($config['no_redirect'])) $this->referer = $_SERVER['REQUEST_URI'];
        $this->showErrorPage();
    }


    /**
     * Locks normal users out from certain pages
     */
    function requireWebmaster()
    {
        if ($this->usermode == USERLEVEL_WEBMASTER) return;
        $this->setError( t('The page you requested requires webmaster rights to view.') );
        $this->showErrorPage();
    }

    /**
     * Locks normal users & webmasters out from certain pages
     */
    function requireAdmin()
    {
        if ($this->usermode == USERLEVEL_ADMIN) return;
        $this->setError( t('The page you requested requires admin rights to view.') );
        $this->showErrorPage();
    }

    /**
     * Locks out everyone except for super-admin from certain pages
     */
    function requireSuperAdmin()
    {
        if ($this->usermode == USERLEVEL_SUPERADMIN) return;
        $this->setError( t('The page you requested requires superadmin rights to view.') );
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
        global $db;

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
