<?php
/**
 * $Id$
 *
 * Holds multiple selection options for UserDataField types such as RADIO
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

// STATUS: wip

require_once('Setting.php');

class UserDataFieldOption
{
    static function get($owner, $name)
    {
        return Setting::get(USERDATA_OPTIONS, $owner, $name);
    }

    static function set($owner, $name, $val)
    {
        return Setting::set(USERDATA_OPTIONS, $owner, $name, $val);
    }

    static function getAll($owner)
    {
        return Setting::getAll(USERDATA_OPTIONS, $owner);
    }

}

?>
