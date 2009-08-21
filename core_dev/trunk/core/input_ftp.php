<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with FTP/FTPES servers
 *
 * upload and download has been verified to work with a ~10MB file (over ftp and ftpes)
 *
 * URL schemes:
 * ftp:// - Classic FTP
 * ftpes:// - FTP over Explicit SSL/TLS
 * ftps:// - FTP over Implicit SSL/TLS (XXX NOT SUPPORTED) - vsftp 2.0.7 support this, try it out
 * sftp:// - SSH FTP (XXX not supported here!!!)
 *
 * http://en.wikipedia.org/wiki/FTPS
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//XXX: rename to "client_ftp.php", it is both input & output...

//XXX verify file get & put properly

class ftp
{
	//XXX to support implicit SSL we might need to do a full ftp implementation, or maybe curl can help us?
	//    more "full" ftp implementation: http://kacke.de/php_samples/source.php?f=ftp.cls.php

	var $scheme, $host, $port, $path;
	var $username = 'anonymous';
	var $password = 'anon@ftp.com';

	var $handle = false, $pipes;

	function __construct()
	{
	}

	function __destruct()
	{
		if (!$this->handle) return;

		ftp_close($this->handle);

		fclose($this->pipes[0]);
		fclose($this->pipes[1]);
	}

	function parse_url($url)
	{
		$p = parse_url($url);
		if (!$p) return false;

		$this->scheme = $p['scheme'];

		$this->path = '/';
		$this->port = 21;

		$this->host = $p['host'];

		if (!empty($p['port'])) $this->port = $p['port'];
		if (!empty($p['path'])) $this->path = $p['path'];

		if (!empty($p['user'])) $this->username = $p['user'];
		if (!empty($p['pass'])) $this->password = $p['pass'];
		return true;
	}

	function connect($url)
	{
		if ($this->handle) return true;
		if (!$this->parse_url($url)) return false;

		switch ($this->scheme) {
		case 'ftp':   $this->handle = ftp_connect($this->host, $this->port); break;
		case 'ftpes': $this->handle = ftp_ssl_connect($this->host, $this->port); break;
		default: die('ftp class: unhandled scheme '.$this->scheme);
		}
		if (!$this->handle) return false;

		if (!ftp_login($this->handle, $this->username, $this->password)) return false;

		$this->pipes = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
		stream_set_blocking($this->pipes[1], 0);
		return true;
	}

	/**
	 * Returns current working directory for the FTP connection
	 */
	function getcwd()
	{
		return $this->path;
	}

	function chdir($path)
	{
		//XXX: verify if remote path exists!
		$this->path = $path;
	}

	function dir($path = '')
	{
		if ($path) $this->chdir($path);

		$list = ftp_rawlist($this->handle, $this->path);
		//XXX parse list
/*
    [1] => drwxr-xr-x    2 1000     1000         4096 Aug 09 15:54 Documents
    [2] => drwxr-xr-x    2 1000     1000         4096 Aug 09 15:54 Music
    [3] => drwxr-xr-x    2 1000     1000         4096 Aug 09 15:54 Pictures
    [4] => drwxr-xr-x    2 1000     1000         4096 Aug 09 15:54 Public
    [5] => drwxr-xr-x    2 1000     1000         4096 Aug 09 15:54 Templates
    [6] => drwxr-xr-x    2 1000     1000         4096 Aug 09 15:54 Videos
    [7] => drwxr-xr-x    3 1000     1000         4096 Aug 10 17:40 dev
    [8] => -rw-r--r--    1 1000     1000          357 Aug 08 23:24 examples.desktop
    [9] => -rwxrwxrwx    1 1000     1000          132 Aug 04 09:52 ipblock.sh
*/

		return $list;
	}

	/**
	 * Get a file from a FTP server
	 * @param $url ftp://usr:pwd@host/file
	 */
	function get($url)
	{
		if (!$this->connect($url)) return false;

		$data = '';

		$ret = ftp_nb_fget($this->handle, $this->pipes[0], $this->path, FTP_BINARY);

		while ($ret == FTP_MOREDATA && !feof($this->pipes[1])) {
			$r = fread($this->pipes[1], 8192);
			if (!$r) break;
			$data .= $r;
			$ret = ftp_nb_continue($this->handle);
		}

		while (!feof($this->pipes[1])) {
			$r = fread($this->pipes[1], 8192);
			if (!$r) break;
			$data .= $r;
		}

		if ($ret != FTP_FINISHED) return false;
		return $data;
	}

	/**
	 * Uploads a file to the ftp
	 *
	 * @param $local_file path to local file
	 * @param $remote_file path to remote file
	 */
	function put($local_file, $remote_file)
	{
		if (!$this->handle) return false;

		return ftp_put($this->handle, $remote_file, $local_file, FTP_BINARY);
	}

}

?>
