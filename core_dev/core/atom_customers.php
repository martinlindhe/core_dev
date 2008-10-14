<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */


/**
 * Looks up a customer name by id
 *
 * \param $_id customer id
 * \return customer name
 */
function getCustomerName($_id)
{
	global $db;
	if (!is_numeric($_id)) return false;

	$q = 'SELECT customerName FROM tblCustomers WHERE customerId='.$_id;
	return $db->getOneItem($q);
}

/**
 * Checks if customer exists
 *
 * \param $name customer name
 * \param $password
 * \return customer id if found
 */
function getCustomerId($name, $password = '')
{
	global $db;

	$q = 'SELECT customerId FROM tblCustomers';
	$q .= ' WHERE customerName="'.$db->escape($name).'"';
	if ($password) $q .= ' AND customerPass="'.$db->escape($password).'"';

	return $db->getOneItem($q);
}

/**
 * Returns all customers
 */
function getCustomers()
{
	global $db;
	return $db->getArray('SELECT * FROM tblCustomers');
}
?>
