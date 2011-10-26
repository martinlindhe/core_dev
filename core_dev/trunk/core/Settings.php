<?php
/**
 * $Id
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: rework into a static class

require_once('constants.php');

class Settings
{
    //default types - use id's from 50 and up for application specified types
    const APPLICATION = 1;  /// XXXX LATER: drop all these constants. must be in major core_dev bump because all databases will break. some places use USER (from constants.php), orher this
    const USER        = 2;
    const CUSTOMER    = 3; ///< ApiCustomer setting

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
        $q =
        'SELECT ownerId FROM tblSettings'.
        ' WHERE categoryId = ? AND settingType = ? AND settingName = ? AND settingValue = ?';
        return Sql::pSelectItem($q, 'iiss', $this->category, $this->type, $name, $val);
    }

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function getTimeSaved($name, $val)
    {
        $q =
        'SELECT timeSaved FROM tblSettings'.
        ' WHERE categoryId = ? AND settingType = ? AND settingName = ? AND settingValue = ?';
        return Sql::pSelectItem($q, 'iiss', $this->category, $this->type, $name, $val);
    }

    function get($name, $default = '')
    {
        $q =
        'SELECT settingValue FROM tblSettings'.
        ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
        $res = Sql::pSelectRow($q, 'iiis', $this->owner, $this->category, $this->type, $name);

        if ($res) return $res['settingValue'];
        return $default;
    }

    function set($name, $val)
    {
        $q =
        'SELECT settingId FROM tblSettings'.
        ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
        if (Sql::pSelectItem($q, 'iiis', $this->owner, $this->category, $this->type, $name)) {
            $q =
            'UPDATE tblSettings SET timeSaved=NOW(), settingValue = ?'.
            ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
            Sql::pUpdate($q, 'siiis', $val, $this->owner, $this->category, $this->type, $name);
        } else {
            $q =
            'INSERT INTO tblSettings SET timeSaved=NOW(),'.
            'ownerId = ?, categoryId = ?, settingType = ?, settingName = ?, settingValue = ?';
            Sql::pInsert($q, 'iiiss', $this->owner, $this->category, $this->type, $name, $val);
        }
        return true;
    }

    function delete($name)
    {
        $q =
        'DELETE FROM tblSettings'.
        ' WHERE ownerId = ? AND categoryId = ? AND settingType = ? AND settingName = ?';
        Sql::pDelete($q, 'iiis', $this->owner, $this->category, $this->type, $name);
        return true;
    }

    /**
     * @return 2d array of all settings for owner
     */
    function getAll()
    {
        $q =
        'SELECT settingId, settingName, settingValue, categoryId'.
        ' FROM tblSettings WHERE settingType = ? AND ownerId = ?';
        return Sql::pSelect($q, 'ii', $this->type, $this->owner);
    }

}

?>
