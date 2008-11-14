<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with FTP servers
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * XXX: file_get_contents() do support FTP url's but it is very crappy.
 * 		Example error "failed to open stream: FTP server reports 550 Could not get file size."
 */
function ftp_get_contents($server, $username, $password, $remote_file)
{
	//TODO allow "ftp://usr:pwd@host/file" as address instead

	$ftp = ftp_connect($server);
	if (!$ftp) return false;

	if (!ftp_login($ftp, $username, $password)) return false;

	$pipes = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
	stream_set_blocking($pipes[1], 0);

	$data = '';

	$ret = ftp_nb_fget($ftp, $pipes[0], $remote_file, FTP_BINARY);

	while ($ret == FTP_MOREDATA && !feof($pipes[1])) {
		$r = fread($pipes[1], 8192);
		if (!$r) break;
		$data .= $r;
		$ret = ftp_nb_continue($ftp);
	}

	while (!feof($pipes[1])) {
		$r = fread($pipes[1], 8192);
		if (!$r) break;
		$data .= $r;
	}

	ftp_close($ftp);

	fclose($pipes[0]);
	fclose($pipes[1]);

	if ($ret != FTP_FINISHED) return false;
	return $data;
}

?>
