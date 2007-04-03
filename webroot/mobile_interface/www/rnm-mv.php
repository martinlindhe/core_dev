<?php
include("_config/config.include.php");
$path='/home/dev/cs_export/pictures/cs_gallery/g_10000/ORG/';
$subs="";
ini_set("open_basedir", $path);
/*foreach($subs as $dir){
	if (is_dir($path.$dir)) {*/
		if ($dh = opendir($path)) {
			while (($file = readdir($dh)) !== false) {
			  if($file!==".." && $file !=="."){
					print "File: $file : type: ".pathinfo($file, PATHINFO_EXTENSION)."<br />";
				}
			}
		closedir($dh);
		}
/*	}
}*/

?>
