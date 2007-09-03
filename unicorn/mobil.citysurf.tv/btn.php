<?
	if (!isset($_GET['mail']) && !isset($_GET['gb'])) die;

	require_once('config.php');
	$user->requireLoggedIn();

	$red = false;

	if (isset($_GET['mail'])) {
		$src_img = 'gfx/btn_mail.png';
		$text = intval($_SESSION['data']['offsets']['mail_offset']);

		$chk = getUnreadMailCount();
		if ($chk) {
			$text = $chk;
			$red = true;
		}
	}

	if (isset($_GET['gb'])) {
		$src_img = 'gfx/btn_gb.png';
		$text = intval($_SESSION['data']['offsets']['gb_offset']);
		
		$chk = gbCountUnread();
		if ($chk) {
			$text = $chk;
			$red = true;
		}
	}

	$im = imagecreatefrompng($src_img);
	if ($red) {
		$text_col = imagecolorallocate($im, 220, 40, 40); //red
	} else {
		$text_col = imagecolorallocate($im, 20, 20, 20);	//black
	}

	header("Content-type: image/png");
	$px     = (imagesx($im)/2) - (7 * strlen($text) / 2);
	imagestring($im, 3, $px, 28, $text, $text_col);
	imagepng($im);
	imagedestroy($im);
?>
