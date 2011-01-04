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
    private $customers = array();
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
        if (is_numeric($owner) && $owner)
            $q .= ' WHERE ownerId='.$owner;

        $list = $db->getArray($q);

        foreach ($list as $row) {
            $c = new ApiCustomer();
            $c->loadFromSql($row);
            $this->customers[] = $c;
        }
    }

}

?>
