<?php

/**
 * $Id$
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

}


?>
