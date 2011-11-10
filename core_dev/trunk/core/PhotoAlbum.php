<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: early wip
 
class PhotoAlbum
{
    var $id;
    var $owner;          ///< 0 = system wide, else it is tblUsers.id
    var $name;
    var $time_created;

    protected static $tbl_name = 'tblPhotoAlbums';

    public static function getByOwner($id)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE owner = ? OR owner = ?'.
        ' ORDER BY owner ASC, name ASC';
        
        $res = Sql::pSelect($q, 'ii', 0, $id);
        
        return SqlObject::loadObjects($res, __CLASS__);
    }

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }
    
    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }
    
}

?>
