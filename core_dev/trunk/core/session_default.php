<?php
/**
 * $Id$
 *
 * Default session class.
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//STATUS: drop! use SessionHandler

require_once('class.Users.php');

require_once('atom_settings.php');  //for storing userdata
require_once('atom_logging.php');   //for logEntry()

class session_default_DEPRECATE
{
    //Aliases of $_SESSION[] variables
    var $lastActive;            ///< last active
    var $started;               ///< timestamp of when the session started
    var $theme = '';            ///< contains the currently selected theme
    var $log_pageviews = false; ///< logs page views to tblPageViews
    var $default_theme = 'default.css';  ///< default theme if none is choosen
    var $allow_themes = false;  ///< allow themes?

    var $userModes = array(
        0 => 'Normal user',
        1 => 'Webmaster',
        2 => 'Admin',
        3 => 'Super admin'
    ); ///< user modes


    /**
     * Kills the current session, clearing all session variables
     */
    function end()
    {
        $this->started = 0;
        $this->username = '';
        $this->ip = 0;
        $this->mode = 0;
        $this->isWebmaster = false;
        $this->isAdmin = false;
        $this->isSuperAdmin = false;
        $this->theme = $this->default_theme;
        $this->referer = '';

        if (!$this->id) return;

        $this->id = 0;
    }




}

?>
