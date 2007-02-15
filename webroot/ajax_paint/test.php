<?
	include('config.php');

	//todo: hantera file upload separat:
	//	- gör en ajax file upload form (går det?)
	//	- ajax-js som ber om pixeldatan i bilden & ritar upp fönstret

	if (!empty($_FILES['file1'])) {
		//todo: databas koppling + gör en enkel fileupload funktion, mer generell än handleFileUpload()...
		$FileData = $_FILES['file1'];
		if (!is_uploaded_file($FileData['tmp_name'])) {
			echo 'file upload error';
			die;
		}
		
		echo 'name: '.$FileData['name'].'<br>';
		echo 'type: '.$FileData['type'].'<br>';

		$img_size = getimagesize($FileData['tmp_name']);
		echo 'getimagesize() type: '.$img_size['mime'].'<br>';
		echo 'dimensions: '.$img_size[0].'x'.$img_size[1].'<br>';
		
		if ($img_size[0] != $config['mmo']['tile']['width'] || $img_size[1] != $config['mmo']['tile']['height']) {
			echo 'Wrong dimensions!';
			unlink($FileData['tmp_name']);
			die;
		}

		$file_lastname = '';
		$file_firstname = $FileData['name'];
		$pos = strrpos($file_firstname, '.');
		if ($pos !== false) {
			$file_lastname = strtolower(substr($file_firstname, $pos));
			$file_firstname = substr($file_firstname, 0, $pos);
		}

		if (!in_array($file_lastname, $config['allowed_image_extensions'])) {
			echo 'blocking bad filetype';
			unlink($FileData['tmp_name']);
			die;
		}
		
		$uploadfile = $config['files']['upload_path'].$FileData['name'];
		if (!move_uploaded_file($FileData['tmp_name'], $uploadfile)) {
			echo 'Failed to move file from '.$FileData['tmp_name'].' to '.$uploadfile;
		}

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>ajax paint</title>
<link rel="stylesheet" href="design/main.css" type="text/css">
<script type="text/javascript" src="js/functions.js"></script>
</head>

<body onLoad="calc_color(); flood_fill();">

<div id="infobox" onClick="flood_fill()">
	flood fill
</div>

<div id="colorselector">
	<form name="colors" action="">
	R: <input name="r" type="text" value="155" size=1 onChange="calc_color()"><br>
	G: <input name="g" type="text" value="255" size=1 onChange="calc_color()"><br>
	B: <input name="b" type="text" value="255" size=1 onChange="calc_color()"><br>
	</form>
</div>

<div id="saveselector" onClick="save_image()">
	save image
</div>

<div id="fileselector">
	<form name="files" action="" enctype="multipart/form-data" method="post">
		<input type="file" name="file1">
		<input type="submit" value="Load image">
	</form>
</div>

<div id="main">
<?
	for ($y=0; $y<$config['mmo']['tile']['height']; $y++) {
		for ($x=0; $x<$config['mmo']['tile']['width']; $x++) {
			echo '<div class="cellbox" id="c'.$x.'_'.$y.'" onClick="draw_tile('.$x.','.$y.')"></div>'."\n";
		}
	}
?>
</div>

</body>
</html>