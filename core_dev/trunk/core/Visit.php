<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//STATUS: wip

class Visit
{
    var $id;
    var $type;  // constants (PROFILE, FILE)
    var $owner;
    var $ref;   // referer (PROFILE ID, FILE ID)
    var $time;

    protected static $tbl_name = 'tblVisits';

    static function store($obj)
    {
        return SqlObject::storeUnique($obj, self::$tbl_name);
    }

    /**
     * Creates a new visit entry
     * @param $type
     * @param $owner_id
     * @param $ref_id
     */
    static function create($type, $owner_id, $ref_id)
    {
        $o = new Visit();
        $o->type  = $type;
        $o->owner = $owner_id;
        $o->ref   = $ref_id;
        $o->time  = sql_datetime( time() );
        self::store($o);
    }

}

?>
