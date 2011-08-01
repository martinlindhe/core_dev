<?php
/**
 * $Id$
 *
 * For special usage with unique tokens (activation, private links)
 *
 * All tokens are 40 byte hex string repserentation of sha1 sums (160 bit)
 * See Settings.php for general key->val storage
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: dont extend from Settings. make its own class? make class fully static
//CODE CLEANUP: use constants.php TOKEN (7) instead of current (4).. will break databases

require_once('Settings.php');

class Token extends Settings
{
    function __construct()
    {
        $this->type = Settings::TOKEN;
    }

    function exists($name, $val)
    {
        $id = $this->getOwner($name, $val);
        if ($id)
            return true;
        return false;
    }

    /** @return true if token is expired or dont exists */
    function isExpired($name, $val, $duration)
    {
        $ts = $this->getTimeSaved($name, $val);
        if (!$ts)
            return true;

        if (ts($ts) < time() - parse_duration($duration))
            return true;

        return false;
    }

    /**
     * Creates a new token for specified $name
     * @return newly created token
     */
    function generate($name)
    {
        if (!$this->owner)
            return false;

        $session = SessionHandler::getInstance();

        do {
            $val = sha1('pOwplopw' . $session->id . mt_rand() . $session->name . 'LAZER!!');

            if (!$this->getOwner($name, $val))
                break;

        } while (1);

        $this->set($name, $val);

        return $val;
    }

}

?>
