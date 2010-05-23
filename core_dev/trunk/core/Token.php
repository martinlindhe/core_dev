<?php
/**
 * $Id$
 *
 * For special usage with unique tokens (activation, private links)
 *
 * All tokens are 40 byte hex string repserentation of sha1 sums (160 bit)
 * See Settings.php for general key->val storage
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

class Token
{
    private $owner;
    private $token_prefix = 'pOwplopw';
    private $token_suffix = 'LAZER!!';

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function get($name)
    {
        $db = SqlHandler::getInstance();

        $q =
        'SELECT value FROM tblTokens'.
        ' WHERE name="'.$db->escape($name).'"';
        if ($this->owner) $q .= ' AND ownerId='.$this->owner;

        $res = $db->getOneItem($q);
        if ($res) return $res;
        return false;
    }

    /**
     * Creates a new token for specified $name
     */
    function generate($name)
    {
        if (!$this->owner)
            return false;

        $db = SqlHandler::getInstance();

        $name = $db->escape($name);

        $val = $this->findFreeToken($name);

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
     * rainbow table proof: session id adjust outcome per user and base url of the site adjust outcome per installation
     */
    private function findFreeToken($name)
    {
        $db = SqlHandler::getInstance();
        $session = SessionHandler::getInstance();
        $page = XmlDocumentHandler::getInstance();

        do {
            $val = sha1($this->token_prefix.mt_rand().$page->getBaseUrl().$session->id.$this->token_suffix);

            $q =
            'SELECT tokenId FROM tblTokens'.
            ' WHERE name="'.$name.'"'.
            ' AND value="'.$val.'"';
            if (!$db->getOneItem($q))
                return $val;
        } while (1);
    }

    /**
     * Returns ownerId of the setting with the unique value $val
     *
     */
    function getOwner($name, $val)
    {
        $db = SqlHandler::getInstance();

        $q =
        'SELECT ownerId FROM tblTokens'.
        ' WHERE name="'.$db->escape($name).'"'.
        ' AND value="'.$db->escape($val).'"';

        return $db->getOneItem($q);
    }

}

?>
