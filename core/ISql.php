<?php
/**
 * $Id$
 *
 * SQL database interface
 *
 * @author Martin Lindhe, 2010-2011 <martin@ubique.se>
 */

//STATUS: ok, look over all use of the get* methods and see if someone is unused

namespace cd;

interface IDB_SQL
{
    public function connect();
    public function disconnect();

//    public function pSelect($q, $fmt, $p1);
}

?>
