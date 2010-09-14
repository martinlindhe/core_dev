<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip - will replace core_dev atom_customers.php

//TODO: rename tblCustomers to tblApiCustomers

require_once('Settings.php');

class ApiCustomer
{
    private $id;
    private $name;

    function __construct($n = 0)
    {
        if ($n)
            $this->loadCustomer($n);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }

    function loadCustomer($n)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT customerId,customerName FROM tblCustomers WHERE';
        if (is_numeric($n))
            $q .= ' customerId='.$n;
        else
            $q .= ' customerName="'.$db->escape($n).'"';

        $res = $db->getOneRow($q);
        if (!$res)
            throw new Exception ('cant load customer '.$n);

        $this->id   = $res['customerId'];
        $this->name = $res['customerName'];
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
}

?>
