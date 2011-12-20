<?php
/**
 * $Id$
 *
 * IMPORTANT! DONT CHANGE NUMBERS, IT WILL BREAK TEH DATABASES!
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

/**
 * GLOBAL TYPE CONSTS
 * defines possible objects and object owners
 *
 * Reserved 1-50. Use a number above 50 for your own types
 */
define('USER',             1);  ///< normal, public userfile
define('NEWS',             2);  ///< news categories
define('CUSTOMER',         3);  ///< ApiCustomer setting
define('WIKI',             4);  ///< category for wiki file attachments, to allow better organization if needed
define('SITE',             5);  ///< for SITE/APP settings etc
define('FILE',             6);  ///< comments for a file
define('TOKEN',            7);  ///< activation tokens etc
define('BLOG',             8);  ///< normal, personal blog category
define('EXTERNAL',         9);  ///< setting type relies on some external component
define('IP',              10);  ///< setting is associated with an IP address, such as a comment (TODO: or a ip block)
define('POLL',            11);  ///< category is owned by a poll
define('USERDATA_OPTION', 20);  ///< used to hold options in tblSettings for UserDataFieldOptions


/**
 * tblCategory.permissions
 */
define('PERM_PUBLIC',  0x01); ///< public category
define('PERM_PRIVATE', 0x02); ///< owner and owner's friends can see the content
define('PERM_HIDDEN',  0x04); ///< only owner can see the content
define('PERM_USER',    0x40); ///< category is created by user
define('PERM_GLOBAL',  0x80); ///< category is globally available to all users


/**
 * session types for different types of user authentication
 * used in tblUsers.type
 */
define('SESSION_REGULAR',  1); ///< internal session handler (default)
define('SESSION_FACEBOOK', 2);

function getSessionTypes()
{
    return array(
    SESSION_REGULAR   => 'Regular',
    SESSION_FACEBOOK  => 'Facebook',
    );
}

/**
 * tblUserGroups.level
 */
define('USERLEVEL_NORMAL',      0);
define('USERLEVEL_WEBMASTER',   1);
define('USERLEVEL_ADMIN',       2);
define('USERLEVEL_SUPERADMIN',  3);

function getUserLevels()
{
    return array(
    USERLEVEL_NORMAL     => 'Normal',
    USERLEVEL_WEBMASTER  => 'Webmaster',
    USERLEVEL_ADMIN      => 'Admin',
    USERLEVEL_SUPERADMIN => 'Super Admin',
    );
}

function getUserLevelName($n)
{
    $x = getUserLevels();
    return $x[ $n ];
}

?>
