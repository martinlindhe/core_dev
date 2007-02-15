<?
	$long_date  = "F j, Y, H:i"; //December 24, 2002, 18:30
	$short_date = "F j, Y";      //December 24, 2002

	define("TODO_ITEM_OPEN",    0); $todo_item_status[TODO_ITEM_OPEN]     = "OPEN";
	define("TODO_ITEM_ASSIGNED",1); $todo_item_status[TODO_ITEM_ASSIGNED] = "ASSIGNED";
	define("TODO_ITEM_CLOSED",  2); $todo_item_status[TODO_ITEM_CLOSED]   = "CLOSED";

	$todo_list[0]="Website";
	$todo_list[1]="Patcher server";
	$todo_list[2]="Patcher client";
	$todo_list[3]="Login server";
	$todo_list[4]="Game server";
	$todo_list[5]="Game client";

	$todo_item_category[0] = "Missing feature";
	$todo_item_category[1] = "Bug";
	
	define("CLOSE_BUG_BOGUS",        0); $close_bug_reason[CLOSE_BUG_BOGUS]        = "BOGUS";
	define("CLOSE_BUG_ALREADYFIXED", 1); $close_bug_reason[CLOSE_BUG_ALREADYFIXED] = "ALREADY FIXED";

	define("CC_INVALID",	0); $cc_name[CC_INVALID]	= "Invalid";
	define("CC_VISA",		1); $cc_name[CC_VISA]		= "Visa";
	define("CC_MASTERCARD",	2); $cc_name[CC_MASTERCARD] = "Mastercard";
	define("CC_AMEX",		3); $cc_name[CC_AMEX]		= "American Express";
	define("CC_DINERS",		4); $cc_name[CC_DINERS]		= "Diners Club / Carte Blanche";
	define("CC_DISCOVER",	5); $cc_name[CC_DISCOVER]	= "Discover";
	define("CC_JCB",		6); $cc_name[CC_JCB]		= "JCB";


	$guild_member_type[0] = "normal member";
	$guild_member_type[1] = "embassador";
	$guild_member_type[2] = "leader";


	$month_long[ 1] = "January";	$month_long[ 2] = "February";
	$month_long[ 3] = "March";		$month_long[ 4] = "April";
	$month_long[ 5] = "May";		$month_long[ 6] = "June";
	$month_long[ 7] = "July";		$month_long[ 8] = "August";
	$month_long[ 9] = "September";	$month_long[10] = "October";
	$month_long[11] = "November";	$month_long[12] = "December";

	$genderName[0] = "Male";		$genderOwner[0] = "his";	$genderRefer[0] = "he";
	$genderName[1] = "Female";		$genderOwner[1] = "her";	$genderRefer[1] = "she";
	
	$raceName[0] = "Human";
	$raceName[1] = "High elf";
	$raceName[2] = "Dwarf";

	/* This is in tblTimezones aswell but we read from here most of the time */
	$timezone[ 0]["name"] = "International Date Line West (IDLW)";	$timezone[ 0]["gmt"] = "-1200";
	$timezone[ 1]["name"] = "Nome (NT)";							$timezone[ 1]["gmt"] = "–1100";
	$timezone[ 2]["name"] = "Havaiian Standard Time (HST)";			$timezone[ 2]["gmt"] = "–1000";
	$timezone[ 3]["name"] = "Ykon Standard (YST)";					$timezone[ 3]["gmt"] = "–0900";
	$timezone[ 4]["name"] = "Pacific Standard (PST)";				$timezone[ 4]["gmt"] = "–0800";
	$timezone[ 5]["name"] = "Mountain Standard (MST)";				$timezone[ 5]["gmt"] = "–0700";
	$timezone[ 6]["name"] = "Central Standard Time (CST)";			$timezone[ 6]["gmt"] = "–0600";
	$timezone[ 7]["name"] = "Eastern Standard Time (EST)";			$timezone[ 7]["gmt"] = "–0500";
	$timezone[ 8]["name"] = "Atlantic Standard (AT)";				$timezone[ 8]["gmt"] = "–0400";
	$timezone[ 9]["name"] = "";										$timezone[ 9]["gmt"] = "–0300";
	$timezone[10]["name"] = "Azores (AT)";							$timezone[10]["gmt"] = "–0200";
	$timezone[11]["name"] = "West Africa (WAT)";					$timezone[11]["gmt"] = "–0100";
	$timezone[12]["name"] = "Greenwich Mean Time (GMT)";			$timezone[12]["gmt"] = "+0000";
	$timezone[13]["name"] = "Central Europe Time (CET)";			$timezone[13]["gmt"] = "+0100";
	$timezone[14]["name"] = "Eastern Europe Time (EET)";			$timezone[14]["gmt"] = "+0200";
	$timezone[15]["name"] = "Baghdad (BT)";							$timezone[15]["gmt"] = "+0300";
	$timezone[16]["name"] = "";										$timezone[16]["gmt"] = "+0400";
	$timezone[17]["name"] = "";										$timezone[17]["gmt"] = "+0500";
	$timezone[18]["name"] = "";										$timezone[18]["gmt"] = "+0600";
	$timezone[19]["name"] = "West Australian Standard (WAS)";		$timezone[19]["gmt"] = "+0700";
	$timezone[20]["name"] = "China Coast (CCT)";					$timezone[20]["gmt"] = "+0800";
	$timezone[21]["name"] = "Japan Standard Time (JST)";			$timezone[21]["gmt"] = "+0900";
	$timezone[22]["name"] = "Australia Central Standard (ACS)";		$timezone[22]["gmt"] = "+0930";
	$timezone[23]["name"] = "Guam Standard (GST)";					$timezone[23]["gmt"] = "+1000";
	$timezone[24]["name"] = "";										$timezone[24]["gmt"] = "+1100";
	$timezone[25]["name"] = "New Zealand Standard (NZST)";			$timezone[25]["gmt"] = "+1200";

?>