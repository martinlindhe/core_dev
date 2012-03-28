<?php
/**
 * $Id$
 *
 * Stores & handles WGS84 coordinates
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//TODO: port code from input_coordinate.php to static methods

define('COORD_EXACT',      1);

// define('COORD_CONTINENT',  10);
// define('COORD_REGION',     11);
define('COORD_COUNTRY',    12);
// define('COORD_CITY',       13);
// define('COORD_ISLAND',     14);
// define('COORD_MOUNTAIN',   15);

// define('COORD_ROAD',       20);
// define('COORD_RAILROAD',   21);
// define('COORD_AIRPORT',    50);

// define('COORD_SUN',        200);
// define('COORD_PLANET',     201);




class Coordinate
{
    var $id;
    var $owner;
    var $type;       ///< COORD_*
    var $country;    ///< 2-letter country code  XXXXX this is google response, whats the ISO naming standard called again...
    var $name;       ///< localized name of the place (according to Google/other result)
    var $latitude;   ///< double 59.332169 = Stockholm, Sweden
    var $longitude;  ///< double 18.062429
    var $accuracy;   ///< accuracy of the deterimned position, from gps sensors
    var $time_saved;

    protected static $tbl_name = 'tblCoordinate';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public static function getByPlace($name, $lat, $long)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE name = ? AND latitude = ? AND longitude = ?';
        $row = Sql::pSelectRow($q, 'sdd', $name, $lat, $long);

        return SqlObject::loadObject($row, __CLASS__);
    }

    public static function getLatestByOwner($type, $owner)
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?'.
        ' ORDER BY time_saved DESC'.
        ' LIMIT 1';
        $row = Sql::pSelectRow($q, 'ii', $type, $owner);

        return SqlObject::loadObject($row, __CLASS__);
    }

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

}

?>
