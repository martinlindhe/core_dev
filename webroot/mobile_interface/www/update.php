<?php
include("_config/online.include.php");
$main=file("harem_vipgallery.csv");
$folder=6;
for($i=0;$i<count($main);$i++){
	if(is_int($i/30000)){
		$folder++;
	}
	list($main_id,$user_id,$file_name,$pht_cmt)=explode(";",$main[$i]);
	$pht_cmt=eregi_replace("\"","",$pht_cmt);
	$file_name=eregi_replace("\"","",$file_name);
	if(strlen($folder)<2) $folder="0".$folder;
	#echo "$main_id | $user_id | $folder | $file_name | $pht_cmt<br />";
	$res=$sql->queryInsert("REPLACE INTO `s_userphoto` SET	main_id='$main_id',	hidden_value = '', user_id='$user_id',	picd='$folder',	old_filename='$file_name',	pht_cmt='$pht_cmt'");
}
?>
