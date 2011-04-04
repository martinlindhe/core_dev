<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

class ApiCustomerItem
{
    static function load($id)
    {
        $q = 'SELECT * FROM tblApiCustomers WHERE customerId = ?';
        return SqlHandler::getInstance()->pSelectRow($q, 'i', $id);
    }
}

class ApiCustomerList
{
    private $customers = array(); ///< array of ApiCustomer objects
    private $owner; ///< UserGroup owner

    function __construct($owner = 0, $partner = 0)
    {
        $this->load($owner, $partner);
    }

    function getCustomers() { return $this->customers; }

    function load($owner = 0, $partner = 0)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblApiCustomers';

        $cond = array();
        if ($owner && is_numeric($owner))
            $cond[] = 'ownerId='.$owner;

        if ($partner && is_numeric($partner))
            $cond[] = 'partnerId='.$partner;

        if (count($cond))
            $q .= ' WHERE '.implode(' AND ', $cond);

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
