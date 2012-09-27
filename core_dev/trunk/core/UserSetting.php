<?php
/**
 * $Id$
 *
 * Helper class for user settings
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

namespace cd;

require_once('constants.php');
require_once('Setting.php');
require_once('UserDataField.php');

class UserSetting
{
    static function get($owner, $name)
    {
        return Setting::get(USER, $owner, $name);
    }

    static function set($owner, $name, $val)
    {
        return Setting::set(USER, $owner, $name, $val);
    }

    static function delete($owner, $name)
    {
        return Setting::delete(USER, $owner, $name);
    }

    static function getEmail($id) { self::get($id, 'email'); }

    static function setEmail($id, $val) { self::set($id, 'email', $val); }

    /**
     * @return 1d array of owner id's matching name & value
     */
    static function getList($name, $value)
    {
        $q =
        'SELECT ownerId FROM tblSettings'.
        ' WHERE settingType = ? AND settingName = ? AND settingValue = ?';
        return Sql::pSelect1d($q, 'iss', USER, $name, $value);
    }

    /**
     * @return 2d array of all settings for owner
     */
    static function getAll($owner)
    {
        return Setting::getAll(USER, $owner);
    }

}

?>
