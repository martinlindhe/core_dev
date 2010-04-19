<?php
/**
 * $Id$
 *
 * Default user class, using tblUsers
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: bad.. need rethinking & rewrite

require_once('user_base.php');
require_once('class.Users.php');

require_once('functions_userdata.php'); //for verifyRequiredUserdataFields()
require_once('atom_moderation.php');    //for isReservedUsername()

class user_default_XXX_DEPRECATED extends user_base
{

    function __construct($conf = array())
    {
        if (isset($conf['minlen_username'])) $this->minlen_username = $conf['minlen_username'];
        if (isset($conf['minlen_password'])) $this->minlen_password = $conf['minlen_password'];
        if (isset($conf['reserved_usercheck'])) $this->reserved_usercheck = $conf['reserved_usercheck'];
        if (isset($conf['userdata'])) $this->userdata = $conf['userdata'];
    }



}

?>
