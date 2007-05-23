<?
	function addFAQ($_q, $_a)
	{
		global $db, $session;
		
		if (!$session->isAdmin) return;

		$q = 'INSERT INTO tblFAQ SET question="'.$db->escape($_q).'",answer="'.$db->escape($_a).'",createdBy='.$session->id.',timeCreated=NOW()';
		return $db->insert($q);
	}
	
	function getFAQ()
	{
		global $db;

		$q = 'SELECT * FROM tblFAQ';		

		return $db->getArray($q);
	}
?>