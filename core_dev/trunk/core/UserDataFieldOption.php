<?php
/**
 * $Id$
 *
 * Holds multiple selection options for UserDataField types such as RADIO
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

// STATUS: early wip

class UserDataFieldOption
{
    static function get($owner, $name)
    {
        return Settings::get(USERDATA_OPTIONS, $owner, $name);
    }

    static function set($owner, $name, $val)
    {
        return Settings::set(USERDATA_OPTIONS, $owner, $name, $val);
    }

    /**
     * @return 2d array of all settings for owner
     */
    static function getAll($owner)
    {
        $q =
        'SELECT settingId, settingName, settingValue, categoryId'.
        ' FROM tblSettings WHERE settingType = ? AND ownerId = ?';
        return Sql::pSelect($q, 'ii', USERDATA_OPTIONS, $owner);
    }

}

?>
