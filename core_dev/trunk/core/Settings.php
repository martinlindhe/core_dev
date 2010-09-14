<?php
/**
 * $Id
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO remove these and make more userdata field types instead:
/*
define('SETTING_APPDATA',      1); ///< setting global to the whole application
define('SETTING_USERDATA',     2); ///< settings used to store personal userdata
define('SETTING_CALLERDATA',   3); ///< settings used to store data of a caller
define('SETTING_EXTERNALDATA', 4); ///< settings used to store data with external ownerid (such as a Facebook id)
*/

class Settings
{
    //default types - use id's from 50 and up for application specified types
    const APPLICATION = 1;
    const USER        = 2;
    const CUSTOMER    = 3; ///< ApiCustomer setting

    var $type;
    var $category = 0;
    private $owner = 0;

    function __construct($type = 0)
    {
        if (is_numeric($type))
            $this->type = $type;
    }

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function get($name, $default = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT settingValue FROM tblSettings';
        $q .= ' WHERE settingType='.$this->type;
        $q .= ' AND categoryId='.$this->category;
        if ($this->owner) $q .= ' AND ownerId='.$this->owner;
        $q .= ' AND settingName="'.$db->escape($name).'"';

        $res = $db->getOneRow($q);
        if ($res) return $res['settingValue'];
        return $default;
    }

    function set($name, $val)
    {
        $db = SqlHandler::getInstance();

        $name = $db->escape($name);
        $val = $db->escape($val);

        $q = 'SELECT settingId FROM tblSettings WHERE ownerId='.$this->owner;
        $q .= ' AND categoryId='.$this->category;
        $q .= ' AND settingType='.$this->type;
        $q .= ' AND settingName="'.$name.'"';
        if ($db->getOneItem($q)) {
            $q = 'UPDATE tblSettings SET settingValue="'.$val.'",timeSaved=NOW() WHERE ownerId='.$this->owner;
            $q .= ' AND categoryId='.$this->category;
            $q .= ' AND settingType='.$this->type;
            $q .= ' AND settingName="'.$name.'"';
            $db->update($q);
        } else {
            $q = 'INSERT INTO tblSettings SET ownerId='.$this->owner.',';
            $q .= 'categoryId='.$this->category.',';
            $q .= 'settingType='.$this->type.',settingName="'.$name.'",';
            $q .= 'settingValue="'.$val.'",timeSaved=NOW()';
            $db->insert($q);
        }
        return true;
    }

}

?>
