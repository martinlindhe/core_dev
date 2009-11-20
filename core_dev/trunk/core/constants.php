<?php
/**
 * $Id$
 *
 * IMPORTANT! DONT CHANGE NUMBERS, IT WILL BREAK DATABASES!
 */

//STATUS: wip.


/**
 * GLOBAL TYPE CONSTS
 *
 * Reserved 1-50. Use a number above 50 for your own types
 *
 * USERS: class.Category.php, class.Comments.php
 */

define('USER',       1);  ///< normal, public userfile
define('NEWS',       2);  ///< news categories
define('BLOG',       3);  ///< normal, personal blog category
define('WIKI',       4);  ///< category for wiki file attachments, to allow better organization if needed


//define('CONTACT',      11); ///< friend relation category, like "Old friends", "Family"
//define('USERDATA',     12); ///< used for multi-choice userdata types. tblCategories.ownerId = tblUserdata.fieldId
//define('POLL',         13); ///< used for multi-choice polls. tblCategories.ownerId = tblPolls.pollId
//define('LANGUAGE',     14); ///< represents a language, for multi-language features
//define('GENERIC',      30); ///< generic type

/*
 * från comments:

	const NEWS       =  1;
	//XXX: only enable types when they are used. some should be depreacated
/*
	const BLOG       =  2; ///< anonymous or registered users comments on a blog
	const FILE       =  3; ///< anonymous or registered users comments on a image
	const TODOLIST   =  4; ///< todolist item comments
	const GENERIC    =  5; ///< generic comment type
	const PASTEBIN   =  6; ///< "pastebin" text. anonymous submissions are allowed
	const SCRIBBLE   =  7; ///< scribble board
	const CUSTOMER   =  8; ///< customer comments
	const FILEDESC   =  9; ///< this is a file description, only one per file can exist
	const ADMIN_IP   = 10; ///< a comment on a specific IP number, written by an admin (only shown to admins), ownerId=geoip number
	const WIKI       = 11; ///< a comment to a wiki article

	//Comment types only meant for the admin's eyes
	const MODERATION = 30; ///< owner = tblModeration.queueId
	const USER       = 31; ///< owner = tblUsers.userId, admin comments for a user

*/




//tblCategory.permissions:
define('PERM_PUBLIC',  0x01); ///< public category
define('PERM_PRIVATE', 0x02); ///< owner and owner's friends can see the content
define('PERM_HIDDEN',  0x04); ///< only owner can see the content

define('PERM_USER',    0x40); ///< category is created by user
define('PERM_GLOBAL',  0x80); ///< category is globally available to all users

?>
