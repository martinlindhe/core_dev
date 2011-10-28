<?php
/**
 * $Id$
 *
 * For special usage with unique tokens (activation, private links)
 *
 * All tokens are 40 byte hex string repserentation of sha1 sums (160 bit)
 * See Setting.php for general key->val storage
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('constants.php');
require_once('Setting.php');

class Token
{
    public static function get($owner, $name, $default = '')
    {
        return Setting::get(TOKEN, $owner, $name, $default);
    }

    public static function exists($name, $val)
    {
        $id = self::getOwner($name, $val);
        if ($id)
            return true;
        return false;
    }

    public static function getOwner($name, $val)
    {
        return Setting::getOwner(TOKEN, $name, $val);
    }

    public static function delete($owner, $name)
    {
        return Setting::delete(TOKEN, $owner, $name);
    }

    /** @return true if token is expired or dont exists */
    public static function isExpired($name, $val, $duration)
    {
        $ts = Setting::getTimeSaved(TOKEN, $name, $val);
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
    public static function generate($owner, $name)
    {
        $session = SessionHandler::getInstance();

        do {
            $val = sha1('pOwplopw' . $session->id . mt_rand() . $session->name . 'LAZER!!');

            if (!Setting::getOwner(TOKEN, $name, $val))
                break;

        } while (1);

        Setting::set(TOKEN, $owner, $name, $val);

        return $val;
    }

}

?>
