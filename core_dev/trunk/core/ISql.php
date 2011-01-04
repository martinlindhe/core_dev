<?php
/**
 * $Id$
 *
 * SQL database interface
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: ok, look over all use of the get* methods and see if someone is unused

interface IDB_SQL
{
    public function connect();
    public function disconnect();

    public function escape($q);

    public function insert($q);
    public function replace($q);
    public function delete($q);
    public function update($q);

    public function getArray($q);
    public function get1dArray($q);
    public function getMappedArray($q);
    public function getOneRow($q);
    public function getOneItem($q);

//    public function pSelect($q, $fmt, $p1);
}

?>
