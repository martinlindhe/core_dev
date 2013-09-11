<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

class SqlQuery
{
    var $query;
    var $error;
    var $time;
    var $prepared = false;
    var $format;            ///< for prepared statements
    var $params;            ///< for prepared statements
}

?>
