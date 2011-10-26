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
        $setting = new Settings(USERDATA_OPTIONS);
        $setting->setOwner($owner);
        return $setting->get($name);
    }

    static function set($owner, $name, $val)
    {
        $setting = new Settings(USERDATA_OPTIONS);
        $setting->setOwner($owner);
        return $setting->set($name, $val);
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
