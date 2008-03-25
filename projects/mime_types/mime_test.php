<?
/**
 * Tests all media types detection.
 */


$dir_handle = opendir(".") or die("Unable to open .");

$all = array();

while ($file = readdir($dir_handle)) 
{
	if ($file == "." || $file == ".." || $file == '.svn' || $file == 'whats_this.txt') continue;
	if ($file == basename($_SERVER['SCRIPT_NAME'])) continue;

	$pos = strrpos($file, '.');
	$ext = substr($file, $pos);

	$all[] = $file;
}
closedir($dir_handle);

asort($all);
echo '<pre>';
foreach ($all as $file) {
	//echo date("Y-m-d H:i:s", filemtime($file))."\t";
	echo filesize($file);
	echo "\t\t".sha1_file($file);
	echo "\t\t$file\n";
}

?>
