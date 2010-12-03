<?php
/**
 * $Id
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

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

        $q =
        'SELECT settingValue FROM tblSettings'.
        ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
        $res = $db->pSelectRow($q, 'iiis', $this->owner, $this->category, $this->type, $name);

        if ($res) return $res['settingValue'];
        return $default;
    }

    function set($name, $val)
    {
        $db = SqlHandler::getInstance();

        $q =
        'SELECT settingId FROM tblSettings'.
        ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
        if ($db->pSelectItem($q, 'iiis', $this->owner, $this->category, $this->type, $name)) {
            $q =
            'UPDATE tblSettings SET timeSaved=NOW(), settingValue = ?'.
            ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
            $db->pUpdate($q, 'si', $val, $this->owner, $this->category, $this->type, $name);
        } else {
            $q =
            'INSERT INTO tblSettings SET timeSaved=NOW(),'.
            'ownerId = ?, categoryId = ?, settingType = ?, settingName = ?, settingValue = ?';
            $db->pInsert($q, 'iiiss', $this->owner, $this->category, $this->type, $name, $val);
        }
        return true;
    }

}

?>
