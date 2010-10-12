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
    private $info;
    private $level = 0; ///< user level
    private $id;

    static function getUserlevels()
    {
        return array(
        USERLEVEL_NORMAL     => 'Normal',
        USERLEVEL_WEBMASTER  => 'Webmaster',
        USERLEVEL_ADMIN      => 'Admin',
        USERLEVEL_SUPERADMIN => 'Super Admin',
        );
    }

    function setName($s) { $this->name = $s; }
    function setInfo($s) { $this->info = $s; }
    function setLevel($n) { if (is_numeric($n)) $this->level = $n; }

    function save()
    {
        $db = SqlHandler::getInstance();

        $q = 'INSERT INTO tblUserGroups SET name="'.$db->escape($this->name).'",info="'.$db->escape($this->info).'",level='.$this->level;
        $this->id = $db->insert($q);

        return $this->id;
    }

}

?>
