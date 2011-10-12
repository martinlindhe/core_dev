<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: remember client ip between requests in order to mitigate session hijacking
//FIXME: session timeout verkar inte funka rätt?!?!? vill kunna ha session i 7 dygn den dör efter nån timme iaf

//TODO: reimplement user-block & ip-block

//TODO: rework tblUsers.userPass to store value as "sha1:xxxxx", "sha512:xxx" in order to simplify future hash change
//      XXX also increase tblUsers.userPass size to 64(512 bits) + 10 (algorithm name) characters

//TODO: calculate password hash by feeding first generated hash to hash algorithm for a number of times (10?)
//      XXX will break all currently used passwords


require_once('CoreBase.php');
require_once('User.php');
require_once('ErrorHandler.php');
require_once('Sql.php');
require_once('SessionStorageHandler.php');

require_once(dirname(__FILE__) . '/../facebook-php-sdk/facebook.php');

class SessionHandler extends CoreBase  ///XXXX should extend from User class ?
{
    static $_instance;             ///< singleton

    var $id;                       ///< internal user id (tblUsers.userId)
    var $type = 'normal';          ///< 'normal' or 'facebook' session
    var $ip;                       ///< current IP address
    var $username;                 ///< stores "facebook id" for facebook users, otherwise unique username
    var $usermode;                 ///< 0=normal user. 1=webmaster, 2=admin, 3=super admin
    var $referer;                  ///< return to this page after login (if user is browsing a part of the site that is blocked by $this->requireLoggedIn() then logs in)
    var $timeout        = 86400;   ///< 24h - max allowed idle time (in seconds) before session times out and user needs to log in again
    var $online_timeout = 1800;    ///< 30m - max idle time before the user is counted as "logged out" in "users online"-lists etc

    var $name;                     ///< session cookie name, needs to be unique for multiple projects on same webhost
    var $start_page;               ///< redirects user to this page (in $config['app']['web_root'] directory) after successful login
    var $logged_out_start_page;    ///< go to specific page? else root page url will be used
    var $error_page = 'coredev/error';  ///< redirects the user to this page to show errors

    var $isWebmaster;              ///< is user webmaster?
    var $isAdmin;                  ///< is user admin?
    var $isSuperAdmin;             ///< is user superadmin?
    protected $last_active;        ///< timestamp of last activity

    var $allow_logins        = true; ///< do app currently allow logins?
    var $allow_registrations = true; ///< do app currently allow registrations?
    protected $encrypt_key   = '';

    var $fb_handle;                ///< points to Facebook object
    var $facebook_app_id;          ///< facebook app id, enables "login with facebook"?
    protected $facebook_secret;    ///< facebook secret
    var $facebook_id;              ///< facebook user id "fbid"

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setName($s) { $this->name = $s; }
    function setTimeout($n)
    {
        if (!is_duration($n))
            throw new Exception ('bad timeout: '.$n);

        $this->timeout = parse_duration($n);
    }
    function setStartPage($s) { $this->start_page = $s; }
    function setLastActive()
    {
        $this->last_active = time();
        Sql::pUpdate('UPDATE tblUsers SET time_last_active = NOW() WHERE id = ?', 'i', $this->id);
    }

    function setEncryptKey($key) { $this->encrypt_key = $key; }
    function getEncryptKey() { return $this->encrypt_key; }

    function getUsername() { return $this->username; }
    function getLastActive() { return $this->last_active; }
    function getTimeout() { return $this->timeout; }

    function setUsername($s) { $this->username = $s; }

    function allowLogins($b) { $this->allow_logins = $b; }
    function allowRegistrations($b) { $this->allow_registrations = $b; }

    /**
     * App must be registed at http://developers.facebook.com/setup
     */
    function setFacebookAuth($app_id, $secret)
    {
        $this->facebook_app_id = $app_id;
        $this->facebook_secret = $secret;

        $this->fb_handle = new Facebook(array(
            'appId'  => $this->facebook_app_id,
            'secret' => $this->facebook_secret
        ));
    }

    /**
     * Handles logins
     *
     * @param $username
     * @param $pwd
     * @return true on success
     */
    function login($username, $pwd, $type = 'normal')
    {
        $error   = ErrorHandler::getInstance();

        if (!$this->allow_logins) {
            $error->add('Logins currently not allowed.');
            return false;
        }

        $username = trim($username);
        $pwd      = trim($pwd);

        switch ($type) {
        case 'normal':
            $user = User::getByName($username);
            break;

        case 'facebook':
            $user = new FacebookUser($username);
            break;
        default: throw new Exception ('hmm '.$type);
        }

        if (!$user || !$user->id) {
            $error->add('Login failed - user not found1');
            return false;
        }

        $q =
        'SELECT COUNT(*) FROM tblUsers'.
        ' WHERE id = ? AND name = ? AND password = ? AND type = ? AND time_deleted IS NULL';

        $x = Sql::pSelectItem($q,
        'issi',
        $user->id,
        $username,
        sha1( $user->id . sha1($this->encrypt_key) . sha1($pwd) ),  // encrypted password
        $user->type
        );

        if (!$x) {
            dp('Failed login attempt: username '.$username);
            $error->add('Login failed - user not found2');
            return false;
        }

        $this->id = $user->id;
        $this->ip = client_ip();
        $this->username = $username;
        $this->type = $type;

        $this->usermode = UserHandler::getUserLevel($user->id);

        if ($this->usermode >= USERLEVEL_WEBMASTER)  $this->isWebmaster  = true;
        if ($this->usermode >= USERLEVEL_ADMIN)      $this->isAdmin      = true;
        if ($this->usermode >= USERLEVEL_SUPERADMIN) $this->isSuperAdmin = true;

        $q =
        'UPDATE tblUsers SET time_last_login = NOW(), time_last_active = NOW(), last_ip = ?'.
        ' WHERE id = ?';

        Sql::pUpdate($q, 'si', client_ip(), $this->id);

        $q = 'INSERT INTO tblLogins SET timeCreated = NOW(), userId = ?, IP = ?, userAgent = ?';
        Sql::pInsert($q, 'iss', $this->id, client_ip(), $_SERVER['HTTP_USER_AGENT'] );

        $_SESSION['id']           = $this->id;
        $_SESSION['username']     = $this->username;
        $_SESSION['usermode']     = $this->usermode;
        $_SESSION['isWebmaster']  = $this->isWebmaster;
        $_SESSION['isAdmin']      = $this->isAdmin;
        $_SESSION['isSuperAdmin'] = $this->isSuperAdmin;
        $_SESSION['referer']      = $this->referer;
        $_SESSION['ip']           = $this->ip;
        $_SESSION['type']         = $this->type;
        $_SESSION['last_active']  = time();
        session_write_close();

        dp($this->username.' logged in from '.$this->ip );

        $error->reset(); // remove previous errors

        return true;
    }

    /**
     * Starts session & loads previous session data if found
     * must be called at beginning of each page request
     */
    function start()
    {
        if (!$this->name)
            throw new Exception ('session name not set');

        $sess_storage = new SessionStorageHandler();

        session_name($this->name);

        ini_set('session.cookie_lifetime', $this->timeout); // in seconds
        ini_set('session.gc_maxlifetime', $this->timeout);  // in seconds

        if (!session_id())
            if (!session_start())
                throw new Exception ('failed to start session');

        if (empty($_SESSION['id']))
            return;

        $page = XmlDocumentHandler::getInstance();

        setcookie($this->name, session_id(), time() + $this->timeout, $page->getRelativeUrl() );

        $this->id           = &$_SESSION['id'];
        $this->username     = &$_SESSION['username'];
        $this->usermode     = &$_SESSION['usermode'];
        $this->isWebmaster  = &$_SESSION['isWebmaster'];
        $this->isAdmin      = &$_SESSION['isAdmin'];
        $this->isSuperAdmin = &$_SESSION['isSuperAdmin'];
        $this->referer      = &$_SESSION['referer'];
        $this->ip           = &$_SESSION['ip'];
        $this->type         = &$_SESSION['type'];
        $this->last_active  = &$_SESSION['last_active'];

        if ($this->type == 'facebook')
            $this->facebook_id = $this->username;
    }

    /** Logs out the user */
    function logout()
    {
        dp($this->username.' logged out');

        if (!$this->id)
            throw new Exception ('already logged out');

        Sql::pUpdate('UPDATE tblUsers SET time_last_logout = NOW() WHERE id = ?', 'i', $this->id);

        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 604800, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        $page = XmlDocumentHandler::getInstance();

        $show_page = $this->logged_out_start_page ? $this->logged_out_start_page : $page->getRelativeUrl();

        header('Location: '.$show_page);
        $this->end();
        die;
    }

    /**
     * Kills the current session, clearing all session variables
     */
    function end()
    {
        $this->id           = 0;
        $this->ip           = '';
        $this->type         = 'normal';
        $this->username     = '';
        $this->usermode     = 0;
        $this->referer      = '';
        $this->isWebmaster  = false;
        $this->isAdmin      = false;
        $this->isSuperAdmin = false;

        if (!empty($_SESSION))
            $_SESSION = array();  //XXXX FIXME: dont destroy $_SESSION['cd_errors'] (error handler messages)

        session_destroy();
    }

    function getUserLevelName()
    {
        return getUserLevelName( UserHandler::getUserLevel($this->id) );
    }

    /**
     * Locks registered users out from certain pages, such as registration page
     */
    function requireLoggedOut()
    {
        if (!$this->id) return;
        $this->showStartPage();
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
        if ($this->ip == '127.0.0.1') return;
        die;
    }

    /**
     * Redirects user to error page
     */
    function showErrorPage()
    {
        $db = SqlHandler::getInstance();

        if ($db instanceof DatabaseMysqlProfiler && $db->getErrorCount()) {
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

    function handleFacebookLogin() /// XXXX move to own class?
    {
        if ($this->facebook_id)
            throw new Exception ('wiee! already handled');

        // Get User ID
        $fbuser = $this->fb_handle->getUser();
        if (!$fbuser)
            return false;

        try {
            $user_profile = $this->fb_handle->api('/me');
        } catch (FacebookApiException $e) {
//            d( $e );
            error_log($e);
            return false;
        }

        $this->type = 'facebook';
        $this->username    = $this->fb_handle->getUser();
        $this->facebook_id = $this->fb_handle->getUser();

        if (!$this->login($this->facebook_id, '', 'facebook'))
            return false;

        // store email from this result
        UserSetting::set($this->id, 'email', $user_profile['email']);

        // store fb_name setting with "name" value
        UserSetting::set($this->id, 'fb_name', $user_profile['name']);

        // fetch picture
        $x = 'https://graph.facebook.com/'.$this->fb_handle->getUser().'?fields=email,name,picture&access_token='.$this->fb_handle->getAccessToken();
        $res = file_get_contents($x);
        $res = json_decode($res);

//d($res);

        // store fb_picture setting with "picture" value
        UserSetting::set($this->id, 'fb_picture', $res->picture);

        return true;
    }

}

?>
