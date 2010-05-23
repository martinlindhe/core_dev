<?php
/**
 * $Id$
 *
 * For special usage with unique tokens (activation, private links)
 * See Settings.php for general key->val storage
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

class Token
{
    private $owner;

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function get($name)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT value FROM tblTokens';
        $q .= ' WHERE name="'.$db->escape($name).'"';
        if ($this->owner) $q .= ' AND ownerId='.$this->owner;

        $res = $db->getOneItem($q);
        if ($res) return $res;
        return false;
    }

    /**
     * Function fails if $name & $val combo already exists
     */
    function set($name, $val)
    {
        if (!$this->owner)
            return false;

        $db = SqlHandler::getInstance();

        $name = $db->escape($name);
        $val = $db->escape($val);

        //verify the token is unique
        $q =
        'SELECT tokenId FROM tblTokens'.
        ' WHERE name="'.$name.'"'.
        ' AND value="'.$val.'"';
        if ($db->getOneItem($q))
            return false;

        //remove users previous token with this name
        $q =
        'DELETE FROM tblTokens'.
        ' WHERE ownerId='.$this->owner.
        ' AND name="'.$name.'"';
        $db->delete($q);

        //write new token
        $q =
        'INSERT INTO tblTokens'.
        ' SET ownerId='.$this->owner.','.
        'name="'.$name.'",'.
        'value="'.$val.'",'.
        'timeSaved=NOW()';
        $db->insert($q);

        return true;
    }

    /**
     * Returns ownerId of the setting with the unique value $val
     *
     */
    function getOwner($name, $val)
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT ownerId FROM tblTokens';
        $q .= ' WHERE name="'.$db->escape($name).'"';
        $q .= ' AND value="'.$db->escape($val).'"';

        return $db->getOneItem($q);
    }

}

?>
