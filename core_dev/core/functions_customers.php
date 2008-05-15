<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

function getCustomerName($custId)
{
	global $db;
	if (!is_numeric($custId)) return false;
	return $db->getOneItem('SELECT customerName FROM tblCustomers WHERE customerId='.$custId);
}

function getCustomerId($custName, $password = '')
{
	global $db;

	$q = 'SELECT customerId FROM tblCustomers WHERE customerName="'.$db->escape($custName).'"';
	if ($password) {
		$q .= ' AND customerPass="'.$db->escape($password).'"';
	}

	return $db->getOneItem($q);
}

function getCustomers()
{
	global $db;
	return $db->getArray('SELECT * FROM tblCustomers');
}
?>
