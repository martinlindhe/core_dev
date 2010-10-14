<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip - will replace core_dev atom_customers.php

require_once('Settings.php');

class ApiCustomer
{
    private $id;
    private $name;
    private $owner; ///< ownerId
    private $password;

    function __construct($n = 0)
    {
        if ($n)
            $this->loadCustomer($n);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getOwner() { return $this->owner; }

    function setPassword($s) { $this->password = $s; }
    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function loadFromSql($row)
    {
        $this->id    = $row['customerId'];
        $this->name  = $row['customerName'];
        $this->owner = $row['ownerId'];
    }

    function loadCustomer($n)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblApiCustomers WHERE';
        if (is_numeric($n))
            $q .= ' customerId='.$n;
        else
            $q .= ' customerName="'.$db->escape($n).'"';

        if ($this->password)
            $q .= ' AND customerPass="'.$db->escape($this->password).'"';

        $res = $db->getOneRow($q);
        if (!$res)
            throw new Exception ('cant load customer '.$n);

        $this->loadFromSql($res);
    }

    function getSetting($key, $default = '')
    {
        $setting = new Settings(Settings::CUSTOMER);
        $setting->setOwner($this->id);
        return $setting->get($key, $default);
    }

    function setSetting($key, $val)
    {
        $setting = new Settings(Settings::CUSTOMER);
        $setting->setOwner($this->id);
        $setting->set($key, $val);
    }

    function save()
    {
        if (!$this->id)
            throw new exception ('XXX save new customer');

        $db = SqlHandler::getInstance();

        $q = 'UPDATE tblApiCustomers SET customerName="'.$this->name.'",ownerId='.$this->owner.' WHERE customerId='.$this->id;
        $db->update($q);
    }
}

?>
