<?php
/**
 * $Id$
 *
 * core_dev handler
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//STATUS: drop & rewrite. keep SessionHandler part, see end of handleSessionEvents()

require_once('core.php');
//require_once('db_mysqli.php');
//require_once('files_default.php');
//require_once('auth_default.php');
//require_once('session_default.php');

require_once('SqlFactory.php');
require_once('ErrorHandler.php');
require_once('AuthHandler.php');
require_once('SessionHandler.php');

class handler
{
    var $db      = false; ///< db driver in use
    var $user    = false; ///< user driver in use

    var $error;           ///< holds last error message. FIXME both auth->error and session->error exists aswell

    /**
     * Constructor. Initializes the session class
     *
     * @param $conf array with session settings
     */
    function __construct($conf = array())
    {
    }

    /**
     * The parameterized factory method
     */
    public static function factory($type, $driver, $conf = array())
    {
        $class = $type.'_'.$driver;
        if (require_once($class.'.php')) {
            return new $class($conf);
        } else {
            throw new Exception('Driver '.$class.' not found');
        }
    }

    /**
     * Load db driver
     */
    function db($driver = 'mysqli', $conf = array())
    {
        $this->db = $this->factory('db', $driver, $conf);

        //XXX remove this hack:
        global $db;
        $db = $this->db;

        return true;
    }

    /**
     * Load user driver
     */
    function user($driver = 'default', $conf = array())
    {
        $this->user = $this->factory('user', $driver, $conf);
        return true;
    }

    function log($str, $level = LOGLEVEL_NOTICE)
    {
        dp("handler->log(): ".$str);
    }

    function handleEvents()
    {
        if ($this->user) $this->handleUserEvents();

        $auth = AuthHandler::getInstance();
        $auth->handleEvents();

        $session = SessionHandler::getInstance();
        $session->handleEvents();
    }

    function handleUserEvents()
    {
        $session = SessionHandler::getInstance();

        //Handle new user registrations. POST to any page with 'register_usr', 'register_pwd' & 'register_pwd2' to attempt registration
        if (!$session->id && isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']) && ($this->auth->allow_registration || !Users::cnt())) {
            $preId = 0;
            if (!empty($_POST['preId']) && is_numeric($_POST['preId'])) $preId = $_POST['preId'];
            $check = $this->user->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2'], USERLEVEL_NORMAL, $preId);
            if (is_numeric($check)) {
                Users::setPassword($check, $_POST['register_pwd'], $_POST['register_pwd'], $this->auth->sha1_key);
                if ($this->auth->mail_activate) {
                    $this->auth->sendActivationMail($check);
                } else {
                    $this->auth->login($_POST['register_usr'], $_POST['register_pwd']);
                }
            } else {
                $this->error = t('Registration failed').', '.$check;
            }
        }
    }

}

?>
