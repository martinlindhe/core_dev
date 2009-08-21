<?php

die; //FIXME: this code is unused & not functional

if (!is_dir($this->upload_dir)) {

	//fixme: check if the path 1 level above "upload_dir" exists:
	if (!realpath($this->upload_dir.'/../')) {
		die('FATAL: Cannot create upload directory at '.$this->upload_dir.'. Please check paths in config.php');
	}

	$h->session->log('Creating upload directory');

	mkdir($this->upload_dir);
	file_put_contents($this->upload_dir.'.htaccess', $this->htaccess);
	file_put_contents($this->upload_dir.'index.html', '');

	if (!is_dir(realpath($this->thumbs_dir))) {
		$h->session->log('Creating thumbs directory');
		mkdir($this->thumbs_dir);
		file_put_contents($this->thumbs_dir.'.htaccess', $this->htaccess);
		file_put_contents($this->thumbs_dir.'index.html', '');
	}
}
?>
