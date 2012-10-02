<?php

// passes thru a image (with optional width & height specified)

namespace cd;

require_once('Image.php');

$name = File::getUploadPath($this->owner);

if (!empty($_GET['w']) && !empty($_GET['h'])) {
    $im = new ImageResizer($name);

    if ($_GET['w'] <= $im->getWidth() && $_GET['h'] <= $im->getHeight())
        $im->resizeAspect($_GET['w'], $_GET['h']);
} else {
    $im = new Image($name);
}
$im->render();

?>
