<?php
/**
 * $Id$
 *
 * IMPORTANT! DONT CHANGE NUMBERS, IT WILL BREAK DATABASES!
 */

//GLOBAL TYPE CONSTS

//tblCategory.categoryType: System categories. Reserved 1-50. Use a number above 50 for your own category types
define('USERFILE',     1); ///< normal, public userfile
define('WIKIFILE',     4); ///< category for wiki file attachments, to allow better organization if needed
define('TODOLIST',     5); ///< todo list categories

define('BLOG',         10); ///< normal, personal blog category
define('CONTACT',      11); ///< friend relation category, like "Old friends", "Family"
define('USERDATA',     12); ///< used for multi-choice userdata types. tblCategories.ownerId = tblUserdata.fieldId
define('POLL',         13); ///< used for multi-choice polls. tblCategories.ownerId = tblPolls.pollId
define('LANGUAGE',     14); ///< represents a language, for multi-language features
define('NEWS',         20); ///< news categories

define('GENERIC',      30); ///< generic type



//tblCategory.permissions:
define('PERM_PUBLIC',  0x01); ///< public category
define('PERM_PRIVATE', 0x02); ///< owner and owner's friends can see the content
define('PERM_HIDDEN',  0x04); ///< only owner can see the content

define('PERM_USER',    0x40); ///< category is created by user
define('PERM_GLOBAL',  0x80); ///< category is globally available to all users

?>
