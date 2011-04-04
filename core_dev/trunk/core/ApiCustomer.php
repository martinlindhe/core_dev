<?php
/**
 * $Id$
 *
 * External customer accounts
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('Settings.php');

class ApiCustomer
{
    private $id;
    private $name;     ///< api account "username"
    private $owner;    ///< UserGroup owner
    private $partner;  ///< tblPartners id
    private $password;

    function __construct($name = '', $password = '')
    {
        if ($name)
            $this->loadCustomer($name, $password);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getOwner() { return $this->owner; }
    function getPartner() { return $this->partner; }

    function setName($s) { $this->name = $s; }
    function setPassword($s) { $this->password = $s; }
    function setPartner($n) { $this->partner = $n; }
    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function loadFromSql($row)
    {
        $this->id      = $row['customerId'];
        $this->name    = $row['customerName'];
        $this->owner   = $row['ownerId'];
        $this->partner = $row['partnerId'];
    }

    function loadCustomer($name, $password = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblApiCustomers WHERE';
        if (is_numeric($name))
            $q .= ' customerId='.$name;
        else
            $q .= ' customerName="'.$db->escape($name).'"';

        if ($password)
            $q .= ' AND customerPass="'.$db->escape($password).'"';

        $res = $db->getOneRow($q);
        if ($res)
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
        $db = SqlHandler::getInstance();

        if (!$this->id) {
            $q = 'INSERT INTO tblApiCustomers SET customerName = ?, customerPass = ?, ownerId = ?, partnerId = ?';
            $this->id = $db->pInsert($q, 'ssii', $this->name, $this->password, $this->owner, $this->partner);
            return;
        }

        $q = 'UPDATE tblApiCustomers SET customerName = ?, ownerId = ?, partnerId = ? WHERE customerId = ?';
        $db->pUpdate($q, 'siii', $this->name, $this->owner, $this->partner, $this->id);
    }
}

?>
