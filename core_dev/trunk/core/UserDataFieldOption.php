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

    static function getAll($owner)
    {
        return Settings::getAll(USERDATA_OPTIONS, $owner);
    }

}

?>
