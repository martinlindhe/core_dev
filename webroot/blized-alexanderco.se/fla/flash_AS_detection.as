//Macromedia Flash Player Version & Revision Detection
//This is designed to support Macintosh, Windows, Linux, PocketPC versions of the Macromedia Flash player.
//All of this code should be on the first frame of the movie before any other code or assets.
////////////USER SETTINGS////////////
//These variables can be set on the object & embed tags In the HTML that hosts this Flash movie.
//They can be set here if preferred.
//-------------------
//the url that the visitor should be sent to if they do not have the required version of Flash.
altContentURL = "http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash";
//-------------------
//the url that the visitor should be sent to if they have the required version of Flash.
//flashContentURL = "flash_content/flash_content.html";
//-------------------
//The required player version necessary to view the content.
contentVersion = 8;
//-------------------
//The 'dot' release of the player necessary to view the content.
contentMajorRevision = 0;
//-------------------
//The revision number of the player necessary to view the content.
contentMinorRevision = 19;
//--------------------
//A flag to autoInstall the player on IE Windows. Basically skips the test here and goes right to the Flash content.
//:TRICKY: This should not be set to true here in ActionScript. If you want to skip the test for IE windows users, set this on the object tag 
//Such as "flash_detection.swf?allowFlashAutoInstall=true"
//It must be set in the object tag NOT the embed tag, otherwise you would skip the detection when in Netscape Win. 
//allowFlashAutoInstall = false;
//--------------------
//A flag to tell the detection code to look up the latest version of the player (see below)
requireLatestVersion = false;
//The following supports "require latest version" and also supports with the rare case when the player versions sometimes get out of synch
//these should be set here in the Flash file and must be updated by the author(you) when Macromedia publishes a new player that you require.
//The following versions should be current as of 01.07.2004
//set up the mac version
MACLatestVersion = 8;
MACLatestMajorRevision = 0;
MACLatestMinorRevision = 24;
//set up the Windows version
WINLatestVersion = 8;
WINLatestMajorRevision = 0;
WINLatestMinorRevision = 24;
//PocketPC version
WINCELatestVersion = 6;
WINCELatestMajorRevision = 0;
WINCELatestMinorRevision = 80;
//Linux version 
UNIXLatestVersion = 7;
UNIXLatestMajorRevision = 0;
UNIXLatestMinorRevision = 63;

//--------------------
//:TRICKY: This deprecated code style below is necessary to support Flash 4.
//This utility splits up the version string into usable parts.
//first check to see that the $version is there at all.
if (eval("$version") eq "") {
	getURL(altContentURL, "_self");
}
i = 1;
playerOS_str = "";
while (substring(eval("$version"), i, 1) ne " ") {
	playerOS_str = playerOS_str add substring(eval("$version"), i, 1);
	i++;
}
playerVersion = "";
i++;
while (substring(eval("$version"), i, 1) ne ",") {
	playerVersion = playerVersion add Number(substring(eval("$version"), i, 1));
	i++;
}
playerMajorRevision = "";
i++;
while (substring(eval("$version"), i, 1) ne ",") {
	playerMajorRevision = playerMajorRevision add Number(substring(eval("$version"), i, 1));
	i++;
}
playerMinorRevision = "";
i++;
while (substring(eval("$version"), i, 1) ne ",") {
	playerMinorRevision = playerMinorRevision add Number(substring(eval("$version"), i, 1));
	i++;
}
//if the user wants to check against the latest version (defined in this flash movie) change all the content version info to these latest version values.
if (requireLatestVersion eq "true") {
	contentVersion = Number(eval(playerOS_str add "LatestVersion"));
	contentMajorRevision = Number(eval(playerOS_str add "LatestMajorRevision"));
	contentMinorRevision = Number(eval(playerOS_str add "LatestMinorRevision"));
}
//go ahead with checking the player against the content rather than latest version
if (allowFlashAutoInstall eq "true" && playerOS_str eq "WIN") {
	//if we want to autoInstall on Windows go right to the content.
	getURL(flashContentURL, "_self");
} else if (playerVersion<contentVersion) {
	getURL(altContentURL, "_self");
} else if (playerVersion>contentVersion) {
	getURL(flashContentURL, "_self");
} else if ((playerVersion eq contentVersion) && (playerMajorRevision<contentMajorRevision)) {
	getURL(altContentURL, "_self");
} else if ((playerVersion eq contentVersion) && (playerMajorRevision eq contentMajorRevision) && (playerMinorRevision< contentMinorRevision)) {
	getURL(altContentURL, "_self");
} else if ((playerVersion eq contentVersion) && (playerMajorRevision eq contentMajorRevision) && (playerMinorRevision >= contentMinorRevision)) {
	getURL(flashContentURL, "_self");
}
// This is the failsafe for when all the above is not understood by player version 3 or below. 
// This must be the last operation in the detection scheme so do not move it up in the sequence
flash3test = 1;
if (flash3test<>1) {
	//This getURL will be called only in Flash versions < 4.0. Please edit the URL as you wish. 
	//Because of backward compatibility requirement, this address cannot be set with a variable.
	getURL("http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlashl", "_self");
}
