<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

class FaqItem
{
    var $id;
    var $question;
    var $answer;
    var $creator;
    var $time_created;

    protected static $tbl_name = 'tblFAQ';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public static function getAll()
    {
        $q = 'SELECT * FROM '.self::$tbl_name;
        return SqlObject::loadObjects($q, __CLASS__);
    }

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name);
    }

    public static function remove($id)
    {
        return SqlObject::deleteById($id, self::$tbl_name);
    }

}

?>
