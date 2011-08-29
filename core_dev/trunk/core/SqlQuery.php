<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

class SqlQuery
{
    var $query;
    var $error;
    var $time;
    var $prepared = false;
    var $format;            ///< f or prepared statements
    var $params;            ///< for prepared statements
}

?>
