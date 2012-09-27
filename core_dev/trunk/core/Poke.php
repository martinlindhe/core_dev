<?php
/**
 * $Id$
 *
 * To send a poke, or a "flirt" to another community member
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

class Poke
{
    var $id;
    var $from;
    var $to;
    var $time;

    protected static $tbl_name = 'tblPokes';

    public static function store($obj)
    {
        return SqlObject::storeUnique($obj, self::$tbl_name);
    }

    /**
     * Creates a new poke
     * @param $to
     */
    public static function send($to)
    {
        $session = SessionHandler::getInstance();

        $o = new Poke();
        $o->from = $session->id;
        $o->to   = $to;
        $o->time = sql_datetime( time() );
        self::store($o);
    }

    public static function getPokes($to)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE `to` = ?';

        $list = Sql::pSelect($q, 'i', $to);
        return SqlObject::loadObjects($list, __CLASS__);
    }

    public static function getUnseenCount($to)
    {
        $q =
        'SELECT COUNT(*) FROM '.self::$tbl_name.
        ' WHERE `to` = ?';

        return Sql::pSelectItem($q, 'i', $to);
    }

}

?>
