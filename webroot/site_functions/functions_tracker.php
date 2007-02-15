<?
	function trackVisitor(&$db, $tracker_id, $track_loc, $track_ref, $remote_ip, $user_agent)
	{
		if (!is_numeric($tracker_id)) return false;

		$track_loc = dbAddSlashes($db, trim($track_loc));
		$track_ref = dbAddSlashes($db, trim($track_ref));

		$remote_ip = IPv4_to_GeoIP($remote_ip);
		$user_agent = dbAddSlashes($db, trim($user_agent));
		
		//todo: gör om allt detta till en stored procedure
		
		//get a locId
		$sql = 'SELECT entryId FROM tblLocations WHERE location="'.$track_loc.'"';
		$locId = dbOneResultItem($db, $sql);
		if (!$locId) {
			$sql = 'INSERT INTO tblLocations SET location="'.$track_loc.'"';
			dbQuery($db, $sql);
			$locId = $db['insert_id'];
		}

		//get a refId
		$sql = 'SELECT entryId FROM tblReferrers WHERE referrer="'.$track_ref.'"';
		$refId = dbOneResultItem($db, $sql);
		if (!$refId) {
			$sql = 'INSERT INTO tblReferrers SET referrer="'.$track_ref.'"';
			dbQuery($db, $sql);
			$refId = $db['insert_id'];
		}
		
		//get a uaId
		$sql = 'SELECT entryId FROM tblUserAgents WHERE UA="'.$user_agent.'"';
		$uaId = dbOneResultItem($db, $sql);
		if (!$uaId) {
			$sql = 'INSERT INTO tblUserAgents SET UA="'.$user_agent.'"';
			dbQuery($db, $sql);
			$uaId = $db['insert_id'];
		}

		$sql = 'INSERT INTO tblTrackEntries SET trackerId='.$tracker_id.',locId='.$locId.',refId='.$refId.',uaId='.$uaId.',IP='.$remote_ip.',timeCreated=NOW()';
		dbQuery($db, $sql);

		$blank_gif = '1x1.gif';
		
		if (file_exists($blank_gif))
 		{
			header('Cache-Control: cache, must-revalidate');
			header('Content-Transfer-Encoding: binary');
			header('Content-Type: image/gif');
			header('Content-Length: '.filesize($blank_gif));
			readfile($blank_gif);
  	}

		//caches the DNS hostname if needed
  	getDNSCacheHostname($remote_ip);
  	
  	//does a WHOIS lookup if needed
  	//getWhoisData($remote_ip);
	}

	/* Returns all entries from a tracker point (tblTrackEntries->trackerId) */
	function getTrackerEntries(&$db, $tracker_id, $order = 'desc')
	{
		if (!is_numeric($tracker_id)) return false;
		
		$sql = 'SELECT * FROM tblTrackEntries WHERE trackerId='.$tracker_id.' ';
		if ($order == 'desc') {
			$sql .= 'ORDER BY timeCreated DESC';
		} else {
			$sql .= 'ORDER BY timeCreated ASC';
		}

		return dbArray($db, $sql);
	}

	/* Returns userAgent entries from a tracker point, one entry per IP is returned (tblTrackEntries->trackerId) */
	//if $trackerPoint is omitted,one userAgent per IP is returned,used in functions_webtrends.php
	function getTrackerBrowserinfoTimeperiod(&$db, $time_from, $time_to, $trackerId = 0)
	{
		if (!is_numeric($trackerId) || !is_numeric($time_from) || !is_numeric($time_to)) return false;

		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql  = 'SELECT t2.UA AS userAgent FROM tblTrackEntries AS t1';
		$sql .= ' INNER JOIN tblUserAgents AS t2 ON (t1.uaId=t2.entryId)';
		$sql .= ' WHERE t1.timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
		if ($trackerId) $sql .= ' AND t1.trackerId='.$trackerId;
		$sql .= ' GROUP BY t1.IP';

		return dbArray($db, $sql);
	}

	/* Returns userAgent entries from a tracker point, one entry per IP is returned (tblTrackEntries->trackerId) */
	function getTrackerBrowserinfo(&$db, $trackerId)
	{
		if (!is_numeric($trackerId)) return false;

		$sql  = 'SELECT t2.UA AS userAgent FROM tblTrackEntries AS t1';
		$sql .= ' INNER JOIN tblUserAgents AS t2 ON (t1.uaId=t2.entryId)';
		$sql .= ' WHERE t1.trackerId='.$trackerId;
		$sql .= ' GROUP BY t1.IP';

		return dbArray($db, $sql);
	}
	

	/* Returns number of entries from a tracker point (tblTrackEntries->trackerId) */
	function getTrackerEntriesCnt(&$db, $tracker_id)
	{
		if (!is_numeric($tracker_id)) return false;

		$sql = 'SELECT COUNT(entryId) FROM tblTrackEntries WHERE trackerId='.$tracker_id;
		return dbOneResultItem($db, $sql);
	}

	/* Returns the number of entries from a tracker point (tblTrackEntries->trackerId) for a period of time */
	function getTrackerEntriesTimeperiodCnt(&$db, $tracker_id, $time_from, $time_to)
	{
		if (!is_numeric($tracker_id) || !is_numeric($time_from) || !is_numeric($time_to)) return false;
		
		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql = 'SELECT COUNT(entryId) FROM tblTrackEntries WHERE trackerId='.$tracker_id.' AND timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
		return dbOneResultItem($db, $sql);
	}

	/* Returns all unique referrer entries from a tracker point (tblTrackEntries->trackerId) */
	function getTrackerEntriesByReferrers(&$db, $tracker_id, $time_from = 0, $time_to = 0)
	{
		if (!is_numeric($tracker_id) || !is_numeric($time_from) || !is_numeric($time_to)) return false;
		
		$sql  = 'SELECT COUNT(t1.refId) AS cnt,t2.referrer FROM tblTrackEntries AS t1';
		$sql .= ' INNER JOIN tblReferrers AS t2 ON (t1.refId=t2.entryId)';
		$sql .= ' WHERE t1.trackerId='.$tracker_id;
		if ($time_from && $time_to) {
			$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
			$date_to   = date('Y-m-d H:i', $time_to);
			$sql .= ' AND t1.timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
		}
		$sql .= ' AND t1.refId!=0 GROUP BY t1.refId ORDER BY cnt DESC';

		return dbArray($db, $sql);
	}
	
	/* Returns all unique referrer entries for all track points (for web trends statistics) */
	function getTrackerEntriesAllReferrers(&$db, $time_from, $time_to)
	{
		if (!is_numeric($time_from) || !is_numeric($time_to)) return false;		

		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql  = 'SELECT COUNT(t1.refId) AS cnt,t2.referrer FROM tblTrackEntries AS t1 ';
		$sql .= 'INNER JOIN tblReferrers AS t2 ON (t1.refId=t2.entryId) ';
		$sql .= 'WHERE t1.timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'" AND t1.refId!=0 GROUP BY t1.refId';		

		return dbArray($db, $sql);
	}

	/* Returns all unique Locations from a tracker point (tblTrackEntries->trackerId) */
	function getTrackerEntriesByLocation(&$db, $tracker_id, $time_from = 0, $time_to = 0)
	{
		if (!is_numeric($tracker_id) || !is_numeric($time_from) || !is_numeric($time_to)) return false;

		$sql  = 'SELECT COUNT(t1.locId) AS cnt,t2.location FROM tblTrackEntries AS t1';
		$sql .= ' INNER JOIN tblLocations AS t2 ON (t1.locId=t2.entryId)';
		$sql .= ' WHERE t1.trackerId='.$tracker_id;
		if ($time_from && $time_to) {
			$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
			$date_to   = date('Y-m-d H:i', $time_to);
			$sql .= ' AND t1.timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
		}
		$sql .= ' AND t1.locId!=0 GROUP BY locId ORDER BY cnt DESC';

		return dbArray($db, $sql);
	}

	/* Returns all unique tracker ID's (tblTrackEntries->trackerId) & frequency for a unique IP */
	//also includes track point location & site name
	function getTrackerFrequencyByIP(&$db, $geo_ip)
	{
		if (!is_numeric($geo_ip)) return false;

		$sql =
			'SELECT t1.trackerId, t2.location, t2.siteId, t3.siteName, COUNT(t1.IP) AS cnt '.
			'FROM tblTrackEntries AS t1 '.
			'INNER JOIN tblTrackPoints AS t2 ON (t1.trackerId=t2.trackerId) '.
			'INNER JOIN tblTrackSites AS t3 ON (t2.siteId=t3.siteId) '.
			'WHERE t1.IP='.$geo_ip.' '.
			'GROUP BY t1.trackerId '.
			'ORDER BY cnt DESC';

		return dbArray($db, $sql);
	}
	
	/* Returns the timestamp from the last visit of this IP */
	function getIPLastVisit(&$db, $geo_ip)
	{
		if (!is_numeric($geo_ip)) return false;
		
		$sql = 'SELECT timeCreated FROM tblTrackEntries WHERE IP='.$geo_ip.' ORDER BY timeCreated DESC LIMIT 0,1';
		return dbOneResultItem($db, $sql);
	}



	/* Returns all tracker entries for a unique IP */
	function getTrackerEntriesByIP(&$db, $geo_ip)
	{
		if (!is_numeric($geo_ip)) return false;

		$sql = 'SELECT t1.*,t2.UA AS userAgent,t3.location,t4.referrer FROM tblTrackEntries AS t1';
		$sql .= ' INNER JOIN tblUserAgents AS t2 ON (t1.uaId=t2.entryId)';
		$sql .= ' INNER JOIN tblLocations AS t3 ON (t1.locId=t3.entryId)';
		$sql .= ' INNER JOIN tblReferrers AS t4 ON (t1.refId=t4.entryId)';
		$sql .= ' WHERE t1.IP='.$geo_ip.' ORDER BY t1.timeCreated DESC';

		return dbArray($db, $sql);
	}

	//Returns all unique IP's from all track points matching this IP range
	function getUniqueIPFromRange(&$db, $geoip_start, $geoip_end)
	{
		if (!is_numeric($geoip_start) || !is_numeric($geoip_end)) return false;

		$sql = 'SELECT IP, COUNT(IP) AS cnt FROM tblTrackEntries WHERE IP BETWEEN '.$geoip_start.' AND '.$geoip_end.' GROUP BY IP ORDER BY IP ASC';
		return dbArray($db, $sql);
	}

	//Returns the number of unique IP's from all track points matching this IP range
	function getUniqueIPCountFromRange(&$db, $geoip_start, $geoip_end)
	{
		if (!is_numeric($geoip_start) || !is_numeric($geoip_end)) return false;

		$sql = 'SELECT COUNT(DISTINCT IP) FROM tblTrackEntries WHERE IP BETWEEN '.$geoip_start.' AND '.$geoip_end;
		return dbOneResultItem($db, $sql);
	}

	/* Returns all unique IP's found in tracking data for a tracker point (tblTrackEntries->trackerId) */
	function getUniqueIPFromTrackerEntries(&$db, $tracker_id, $time_from = 0, $time_to = 0)
	{
		if (!is_numeric($tracker_id) || !is_numeric($time_from) || !is_numeric($time_to)) return false;

		$sql  = 'SELECT DISTINCT(t1.IP),COUNT(t1.IP) AS cnt FROM tblTrackEntries AS t1 ';
		$sql .= 'WHERE t1.trackerId='.$tracker_id;
		if ($time_from && $time_to) {
			$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
			$date_to   = date('Y-m-d H:i', $time_to);
			$sql .= ' AND t1.timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
		}
		$sql .= ' GROUP BY t1.IP ORDER BY cnt DESC';

		return dbArray($db, $sql);
	}
	
	/* Returns the number of unique IP's for this trackpoint & timespan */
	function getUniqueIPCountFromTrackerEntriesTimeperiod(&$db, $tracker_id, $time_from, $time_to, $exclude_private = false)
	{
		if (!is_numeric($tracker_id) || !is_numeric($time_from) || !is_numeric($time_to) || !is_bool($exclude_private)) return false;

		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql  = 'SELECT COUNT(DISTINCT IP) FROM tblTrackEntries AS t1 ';
		if ($exclude_private) $sql .= ' INNER JOIN dbGeoIP.tblWHOIS AS t2 ON (t1.IP BETWEEN t2.geoIP_start AND t2.geoIP_end)';
		$sql .= 'WHERE t1.trackerId='.$tracker_id.' AND t1.timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
		if ($exclude_private) $sql .= ' AND t2.privateRange=0';

		return dbOneResultItem($db, $sql);
	}

	function createTrackSite(&$db, $siteName, $siteNote)
	{
		$siteName = dbAddSlashes($db, $siteName);
		$siteNote = dbAddSlashes($db, $siteNote);
		
		$sql = 'INSERT INTO tblTrackSites SET creatorId='.$_SESSION['userId'].',timeCreated=NOW(),siteName="'.$siteName.'",siteNotes="'.$siteNote.'"';
		dbQuery($db, $sql);

		return $db['insert_id'];
	}

	function setTrackSiteName(&$db, $siteId, $name)
	{
		if (!is_numeric($siteId)) return false;

		$name = dbAddSlashes($db, $name);

		$sql = 'UPDATE tblTrackSites SET siteName="'.$name.'",timeEdited=NOW(),editorId='.$_SESSION['userId'].' WHERE siteId='.$siteId;
		dbQuery($db, $sql);
	}

	function setTrackSiteNote(&$db, $siteId, $note)
	{
		if (!is_numeric($siteId)) return false;

		$note = dbAddSlashes($db, $note);

		$sql = 'UPDATE tblTrackSites SET siteNotes="'.$note.'",timeEdited=NOW(),editorId='.$_SESSION['userId'].' WHERE siteId='.$siteId;
		dbQuery($db, $sql);
	}

	/* If creatorId = 0 return all */
	function getTrackSites(&$db, $creatorId = 0)
	{
		if (!is_numeric($creatorId)) return false;
		
		$sql =
			'SELECT t1.*,COUNT(t2.trackerId) AS cnt '.
			'FROM tblTrackSites AS t1 '.
			'LEFT OUTER JOIN tblTrackPoints AS t2 ON (t1.siteId=t2.siteId) ';
		if ($creatorId) $sql .= 'WHERE t1.creatorId='.$creatorId.' ';
		$sql .= 'GROUP BY t1.siteId';

		return dbArray($db, $sql);
	}
	
	function getTrackSite(&$db, $siteId)
	{
		if (!is_numeric($siteId)) return false;
		
		$sql = 'SELECT * FROM tblTrackSites WHERE siteId='.$siteId;
		
		return dbOneResult($db, $sql);
	}

	function createTrackPoint(&$db, $siteId, $location, $note)
	{
		if (!is_numeric($siteId)) return false;

		$location = dbAddSlashes($db, $location);
		$note = dbAddSlashes($db, $note);
		
		$sql = 'INSERT INTO tblTrackPoints SET siteId='.$siteId.',location="'.$location.'",trackerNotes="'.$note.'",creatorId='.$_SESSION['userId'].',timeCreated=NOW()';
		dbQuery($db, $sql);

		return $db['insert_id'];
	}
	
	function setTrackPointNote(&$db, $tracker_id, $note)
	{
		if (!is_numeric($tracker_id)) return false;

		$note = dbAddSlashes($db, $note);

		$sql = 'UPDATE tblTrackPoints SET trackerNotes="'.$note.'",timeEdited=NOW(),editorId='.$_SESSION['userId'].' WHERE trackerId='.$tracker_id;
		dbQuery($db, $sql);
	}

	function setTrackPointLocation(&$db, $tracker_id, $location)
	{
		if (!is_numeric($tracker_id)) return false;

		$location = dbAddSlashes($db, $location);

		$sql = 'UPDATE tblTrackPoints SET location="'.$location.'",timeEdited=NOW(),editorId='.$_SESSION['userId'].' WHERE trackerId='.$tracker_id;
		dbQuery($db, $sql);
	}
	
	/* Returns all track points for $siteId + count of track entries for each */
	function getTrackPoints(&$db, $siteId)
	{
		if (!is_numeric($siteId)) return false;

		$sql = 'SELECT * FROM tblTrackPoints WHERE siteId='.$siteId;

		return dbArray($db, $sql);
	}

	/* Returns count of track points for $siteId */
	function getTrackPointsCount(&$db, $siteId)
	{
		if (!is_numeric($siteId)) return false;

		$sql = 'SELECT COUNT(trackerId) FROM tblTrackPoints WHERE siteId='.$siteId;

		return dbOneResultItem($db, $sql);
	}
	
	/* Returns the ID of the first created track point for $siteId */
	function getOldestTrackPointID(&$db, $siteId)
	{
		if (!is_numeric($siteId)) return false;

		$sql = 'SELECT trackerId FROM tblTrackPoints WHERE siteId='.$siteId.' ORDER BY timeCreated ASC LIMIT 0,1';
		return dbOneResultItem($db, $sql);
	}
	
	/* Get the timestamp of the first created entry fo tblTrackEntries->trackerId */
	function getOldestTrackPointTime(&$db, $trackerId)
	{
		if (!is_numeric($trackerId)) return false;
		
		$sql = 'SELECT timeCreated FROM tblTrackEntries WHERE trackerId='.$trackerId.' ORDER BY timeCreated ASC LIMIT 1';
		return dbOneResultItem($db, $sql);
	}

	/* Get the timestamp of the last created entry fo tblTrackEntries->trackerId */
	function getNewestTrackPointTime(&$db, $trackerId)
	{
		if (!is_numeric($trackerId)) return false;
		
		$sql = 'SELECT timeCreated FROM tblTrackEntries WHERE trackerId='.$trackerId.' ORDER BY timeCreated DESC LIMIT 1';
		return dbOneResultItem($db, $sql);
	}


	function getTrackPoint(&$db, $trackerId)
	{
		if (!is_numeric($trackerId)) return false;

		$sql = 'SELECT t1.*,t2.siteName FROM tblTrackPoints AS t1 '.
			'INNER JOIN tblTrackSites AS t2 ON (t1.siteId=t2.siteId) '.
			'WHERE t1.trackerId='.$trackerId;

		return dbOneResult($db, $sql);
	}
	
	/* Deletes this track point & all it's collected entries */
	function deleteTrackPoint(&$db, $trackerId)
	{
		if (!is_numeric($trackerId)) return false;
		
		$sql = 'DELETE FROM tblTrackPoints WHERE trackerId='.$trackerId;
		dbQuery($db, $sql);
		
		clearTrackPoint($db, $trackerId);
	}
	
	/* Deletes all entries from this track point */
	function clearTrackPoint(&$db, $trackerId)
	{
		if (!is_numeric($trackerId)) return false;
		
		$sql = 'DELETE FROM tblTrackEntries WHERE trackerId='.$trackerId;
		dbQuery($db, $sql);
	}

	function deleteTrackSite(&$db, $siteId)
	{
		if (!is_numeric($siteId)) return false;
		
		$sql = 'DELETE FROM tblTrackSites WHERE siteId='.$siteId;
		dbQuery($db, $sql);
	}


	//helper function to sanitize user agent string, location & referrer strings
	function url_safedecode($str)
	{
		$str = trim(urldecode($str));
		$str = str_replace('&', '&amp;', $str);
		$str = str_replace('"', '&quot;', $str);

		//blocks html code injection attempts:
		$str = str_replace('<', '&lt;', $str);
		$str = str_replace('>', '&gt;', $str);
		
		return $str;
	}
	
	function MakeTrackerBox($title, $content, $strip = true)
	{
		if ($strip) $content = htmlspecialchars($content, ENT_NOQUOTES, 'UTF-8');

		$content = nl2br(trim($content));

		/*$str =
			'<div style="height: 100%; width: 300px; background-color: #CCC;">'.
				$title.
				'<div style="height: 15px; text-align: right; background-color: #EEE;">'.$content.'</div>'.
			'</div>';*/
			
		$str =
			'<div class="bb_quote">'.
			'<div class="bb_quote_head">'.$title.'</div>'.
			'<div class="bb_quote_body">'.$content.'</div>'.
			'</div>';

		return $str;
	}

	/* Returns an array with entries for each day where there is track data for this track point */
	function getTrackerMonthOverview(&$db, $trackerId, $year, $month)
	{
		if (!is_numeric($trackerId) || !is_numeric($year) || !is_numeric($month)) return false;

		$sql =	'SELECT DAY(timeCreated) FROM tblTrackEntries '.
						'WHERE trackerId='.$trackerId.' AND '.
						'YEAR(timeCreated)='.$year.' AND MONTH(timeCreated)='.$month.' '.
						'GROUP BY DAYOFMONTH(timeCreated)';

		return dbOneArray($db, $sql);
	}



	
	/* Returns the total number of track entries (from all track sites) for specified time period */
	function getTotalTrackerEntries(&$db, $time_from, $time_to)
	{
		if (!is_numeric($time_from) || !is_numeric($time_to)) return false;
		
		$date_from = date('Y-m-d H:i', $time_from); // YYYY-MM-DD HH:SS format (MySQL DATETIME)
		$date_to   = date('Y-m-d H:i', $time_to);

		$sql = 'SELECT COUNT(entryId) FROM tblTrackEntries WHERE timeCreated BETWEEN "'.$date_from.'" AND "'.$date_to.'"';

		return dbOneResultItem($db, $sql);
	}

?>