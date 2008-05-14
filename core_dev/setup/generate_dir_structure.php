<?php

//fixme: denna kod anropas & används ej ännu av något setup script

if (!is_dir($this->upload_dir)) {

	//fixme: check if the path 1 level above "upload_dir" exists:
	if (!realpath($this->upload_dir.'/../')) {
		die('FATAL: Cannot create upload directory at '.$this->upload_dir.'. Please check paths in config.php');
	}

	$session->log('Creating upload directory');

	mkdir($this->upload_dir);
	file_put_contents($this->upload_dir.'.htaccess', $this->htaccess);
	file_put_contents($this->upload_dir.'index.html', '');

	if (!is_dir(realpath($this->thumbs_dir))) {
		$session->log('Creating thumbs directory');
		mkdir($this->thumbs_dir);
		file_put_contents($this->thumbs_dir.'.htaccess', $this->htaccess);
		file_put_contents($this->thumbs_dir.'index.html', '');
	}
}
?>
