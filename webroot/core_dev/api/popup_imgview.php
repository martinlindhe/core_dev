<?
/*
	html_imageview.php - popupwindow that displays a image resource

	todo:
		* display ajax loading wheel in place of image
		* hover mouse over image to highlight controls: cut, resize, rotate, convert, etc
*/
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
	$fileId = $_GET['id'];

	require_once('find_config.php');

	createXHTMLHeader();

	echo '<img id="popup_img" src="'.$config['core_web_root'].'api/file.php?id='.$fileId.getProjectPath().'"/>';
?>
<script type="text/javascript">
if (image_loaded('popup_img')) resize_wnd_to_img('popup_img');
</script>
