<?php
/**
 * $Id$
 *
 * IMPORTANT! DONT CHANGE NUMBERS, IT WILL BREAK DATABASES!
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip


/**
 * GLOBAL TYPE CONSTS
 *
 * Reserved 1-50. Use a number above 50 for your own types
 *
 * USERS: CategoryItem.php, class.Comments.php
 */


// defines possible objects and object owners
define('USER',       1);  ///< normal, public userfile
define('NEWS',       2);  ///< news categories
define('CUSTOMER',   3);  ///< ApiCustomer setting
define('WIKI',       4);  ///< category for wiki file attachments, to allow better organization if needed
define('SITE',       5);  ///< for SITE/APP settings etc
define('FILE',       6);  ///< comments for a file
define('TOKEN',      7);  ///< activation tokens etc
define('BLOG',       8);  ///< normal, personal blog category
define('EXTERNAL',   9);  ///< setting type relies on some external component
define('IP',        10);  ///< setting is associated with an IP address, such as a comment (TODO: or a ip block)

define('USERDATA_OPTIONS', 20);  ///< used to hold options in tblSettings for UserDataFieldOptions



//XXX: only enable types when they are used. some should be depreacated
/*
define('CONTACT',      11); ///< friend relation category, like "Old friends", "Family"
define('LANGUAGE',     14); ///< represents a language, for multi-language features

const TODOLIST   =  4; ///< todolist item comments
const GENERIC    =  5; ///< generic comment type
const PASTEBIN   =  6; ///< "pastebin" text. anonymous submissions are allowed
const SCRIBBLE   =  7; ///< scribble board
const ADMIN_IP   = 10; ///< a comment on a specific IP number, written by an admin (only shown to admins), ownerId=geoip number
*/



//tblCategory.permissions:
define('PERM_PUBLIC',  0x01); ///< public category
define('PERM_PRIVATE', 0x02); ///< owner and owner's friends can see the content
define('PERM_HIDDEN',  0x04); ///< only owner can see the content

define('PERM_USER',    0x40); ///< category is created by user
define('PERM_GLOBAL',  0x80); ///< category is globally available to all users

?>
