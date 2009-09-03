<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Send email to multiple users
 */
function contact_users($message, $subject, $all, $presvid, $logged_in_days, $days, $res)
{
	global $h, $db;
	if (empty($message) || empty($subject)) return false;

	if ($all == 1) { // Ignore everything else, just get a list of all users.
		$users = Users::getUsers();

		foreach ($users as $row) {
			$email = loadUserdataEmail($row['userId']);
			echo 'All users.<br/>';
			smtp_mail($email, $subject, $message);
		}
	} else {
		foreach ($res as $row) {
			if (!empty($days)) {
				if (!is_numeric($days)) return false;
				$timestamp = strtotime('-'.$days.' day');
				$logintime = datetime_to_timestamp(Users::getLogintime($row['userId']));

				// user logged in before timestamp (so hasnt been logged in the latest $days days)
				if ($logged_in_days == 1 && $logintime < $timestamp) {
					// Then it's wrong, so dont send email
					continue;
				} else if ($logged_in_days == 0 && $logintime > $timestamp) {
					continue;
				}
			}

			//FIXME denna kod är m2w-specifik och har inget att göra i core_dev. urval borde göras efter USERDATA_TYPE_VIDEOPRES
			if (!empty($presvid)) {
				if ($presvid == 1) {
					$cId = loadSetting(SETTING_USERDATA, 0, $row['userId'], 'm2w_id');
					if (!$cId) continue;
					$vid_pres = $h->files->getFiles(FILETYPE_VIDEOPRES, $cId);
					if (!is_array($vid_pres)) continue;
				}
			}
			$email = loadUserdataEmail($row['userId']);
			echo $email.'<br/>';
			smtp_mail($email, $subject, $message);
		}
	}
}

?>
