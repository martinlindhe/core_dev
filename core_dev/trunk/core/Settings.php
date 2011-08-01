<?php
/**
 * $Id
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: rework into a static class

require_once('constants.php');

class SettingsByOwner  // XXX rename
{
    /**
     * @return 1d array of owner id's matching type, name & value
     */
    static function getList($type, $name, $value)  //XXXX rename
    {
        $q = 'SELECT ownerId FROM tblSettings WHERE settingType = ? AND settingName = ? AND settingValue = ?';
        return SqlHandler::getInstance()->pSelect1d($q, 'iss', $type, $name, $value);
    }

    /**
     * @return 2d array of all settings matching type & owner
     */
    static function getAll($type, $owner) // XXX rename
    {
        $q = 'SELECT settingId, settingName, settingValue, categoryId FROM tblSettings WHERE settingType = ? AND ownerId = ?';
        return SqlHandler::getInstance()->pSelect($q, 'ii', $type, $owner);
    }
}

class Settings
{
    //default types - use id's from 50 and up for application specified types
    const APPLICATION = 1;  /// XXXX LATER: drop all these constants. must be in major core_dev bump because all databases will break. some places use USER (from constants.php), orher this
    const USER        = 2;
    const CUSTOMER    = 3; ///< ApiCustomer setting
    const TOKEN       = 4; ///< Token setting

    protected $type     = 0;
    protected $category = 0;
    protected $owner    = 0;

    function __construct($type = 0)
    {
        if (is_numeric($type))
            $this->type = $type;
    }

    /**
     * @return ownerId of the setting with the unique value $val
     */
    function getOwner($name, $val)
    {
        $db = SqlHandler::getInstance();

        $q =
        'SELECT ownerId FROM tblSettings'.
        ' WHERE categoryId = ? AND settingType = ? AND settingName = ? AND settingValue = ?';
        return $db->pSelectItem($q, 'iiss', $this->category, $this->type, $name, $val);
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
            $db->pUpdate($q, 'siiis', $val, $this->owner, $this->category, $this->type, $name);
        } else {
            $q =
            'INSERT INTO tblSettings SET timeSaved=NOW(),'.
            'ownerId = ?, categoryId = ?, settingType = ?, settingName = ?, settingValue = ?';
            $db->pInsert($q, 'iiiss', $this->owner, $this->category, $this->type, $name, $val);
        }
        return true;
    }

    function delete($name)
    {
        $db = SqlHandler::getInstance();

        $q =
        'DELETE FROM tblSettings'.
        ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
        $db->pDelete($q, 'iiis', $this->owner, $this->category, $this->type, $name);
        return true;
    }

}

?>
