<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class UserGroup
{
    private $name;
    private $id;
    private $level = 0; ///< user level
    private $info;

    static function getUserlevels()
    {
        return array(
        USERLEVEL_NORMAL     => 'Normal',
        USERLEVEL_WEBMASTER  => 'Webmaster',
        USERLEVEL_ADMIN      => 'Admin',
        USERLEVEL_SUPERADMIN => 'Super Admin',
        );
    }

    function getName() { return $this->name; }
    function getInfo() { return $this->info; }
    function getLevel() { return $this->level; }

    function getLevelDesc()
    {
        $x = $this->getUserlevels();
        return $x[ $this->level ];
    }

    function setName($s) { $this->name = $s; }
    function setInfo($s) { $this->info = $s; }
    function setLevel($n) { if (is_numeric($n)) $this->level = $n; }

    function loadFromSql($row)
    {
        $this->name  = $row['name'];
        $this->id    = $row['groupId'];
        $this->level = $row['level'];
        $this->info  = $row['info'];
    }

    function save()
    {
        $db = SqlHandler::getInstance();

        $q = 'INSERT INTO tblUserGroups SET name="'.$db->escape($this->name).'",info="'.$db->escape($this->info).'",level='.$this->level;
        $this->id = $db->insert($q);

        return $this->id;
    }

}

?>
