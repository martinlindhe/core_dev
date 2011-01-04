<?php
/**
 * $Id$
 *
 * For special usage with unique tokens (activation, private links)
 *
 * All tokens are 40 byte hex string repserentation of sha1 sums (160 bit)
 * See Settings.php for general key->val storage
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('Settings.php');

class Token extends Settings
{
    private $token_prefix = 'pOwplopw';
    private $token_suffix = 'LAZER!!';

    function __construct()
    {
        $this->type = Settings::TOKEN;
    }

    /**
     * Creates a new token for specified $name
     * @return newly created token
     */
    function generate($name)
    {
        if (!$this->owner)
            return false;

        $val = $this->findFreeToken($name);

        $this->set($name, $val);

        return $val;
    }

    /**
     * rainbow table proof: session id adjust outcome per user and base url of the site adjust outcome per installation
     */
    private function findFreeToken($name)
    {
        $session = SessionHandler::getInstance();

        do {
            $val = sha1($this->token_prefix . mt_rand() . $session->id . mt_rand() . $this->token_suffix);

            if (!$this->getOwner($name, $val))
                return $val;

        } while (1);
    }

}

?>
