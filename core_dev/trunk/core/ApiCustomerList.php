<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

class ApiCustomerList
{
    private $customers = array(); ///< array of ApiCustomer objects
    private $owner; ///< UserGroup owner

    function __construct($owner = 0)
    {
        $this->load($owner);
    }

    function getCustomers() { return $this->customers; }

    function load($owner = 0)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblApiCustomers';
        if ($owner && is_numeric($owner))
            $q .= ' WHERE ownerId='.$owner;

        $list = $db->getArray($q);

        foreach ($list as $row) {
            $c = new ApiCustomer();
            $c->loadFromSql($row);
            $this->customers[] = $c;
        }
    }

    /** returns array with id=>name pairs */
    static function getList()
    {
        $db = SqlHandler::getInstance();

        $list = array();

        $q = 'SELECT * FROM tblApiCustomers';
        foreach ($db->getArray($q) as $row)
            $list[ $row['customerId'] ] = $row['customerName'];

        return $list;
    }

}

?>
