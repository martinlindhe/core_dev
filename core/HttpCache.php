<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2012 <martin@ubique.se>
 */

namespace cd;

class HttpCache
{
    var $id;
    var $url;
    var $time_saved;
    var $raw;

    protected static $tbl_name = 'tblHttpCache';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

}
