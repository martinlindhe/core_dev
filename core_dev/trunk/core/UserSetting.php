<?php
/**
 * $Id$
 *
 * Helper class for user settings
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

require_once('constants.php');
require_once('Settings.php');

class UserSetting
{
    static function get($id, $name)
    {
        $setting = new Settings(USER);
        $setting->setOwner($id);
        return $setting->get($name);
    }

    static function set($id, $name, $val)
    {
        $setting = new Settings(USER);
        $setting->setOwner($id);
        return $setting->set($name, $val);
    }

    static function delete($id, $name)
    {
        $setting = new Settings(USER);
        $setting->setOwner($id);
        return $setting->delete($name);
    }

    static function setEmail($id, $val)
    {
        self::set($id, 'email', $val);
    }

}

?>
