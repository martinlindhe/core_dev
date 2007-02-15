<?
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];
	
	$view = 0;
	if (isset($_GET['view'])) $view = 1;

	include('include_all.php');

	$data = getFile($db, $fileId);
	if (!$data) die;

	header('Content-Type: ' . $data['fileMime']);

	//These headers allows the browser to cache the images for 30 days. Works with MSIE6 and Firefox 1.5
	header("Expires: " . date("D, j M Y H:i:s", time() + (86400 * 30)) . " UTC");
	header("Cache-Control: Public");
	header("Pragma: Public");

	if (!$view) {
		/* Prompts the user to save the file */
		header('Content-Disposition: attachment; filename="'.basename($data['fileName']).'"');
	} else {
		header('Content-Disposition: inline; filename="'.basename($data['fileName']).'"');
	}
	header('Content-Transfer-Encoding: binary');

	$filename = $config['upload_dir'].$data['fileId'];

	header('Content-Length: '. $data['fileSize']);
	echo file_get_contents($filename);
	die;
	
?>