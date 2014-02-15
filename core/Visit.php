<?php
/**
 * $Id$
 *
 * For keeping vistor track of user profiles, photos etc
 *
 * @author Martin Lindhe, 2012 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

class Visit
{
    var $id;
    var $type;  // constants (PROFILE, FILE)
    var $owner;
    var $ref;   // referer (PROFILE ID, FILE ID)
    var $time;

    protected static $tbl_name = 'tblVisits';

    /**
     * Creates a new visit entry
     * @param $type
     * @param $owner_id
     * @param $ref_id
     */
    public static function create($type, $owner_id, $ref_id)
    {
        $o = new Visit();
        $o->type  = $type;
        $o->owner = $owner_id;
        $o->ref   = $ref_id;
        $o->time  = sql_datetime( time() );
        $o->store();
    }

    public static function getAll($type, $owner)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?';

        $list = Sql::pSelect($q, 'ii', $type, $owner);
        return $list;
    }

    public function store()
    {
        return SqlObject::storeUnique($this, self::$tbl_name);
    }

}
