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
	
	function showFAQ()
	{
		$list = getFAQ();
		if (!$list) return;
		
		$active = $list[0]['faqId'];

		//FAQ full Q&A details
		for ($i=0; $i<count($list); $i++) {
			echo '<div class="faq_holder">';
				echo '<div class="faq_q" onclick="faq_focus('.$i.')">';
					echo ($i+1).'. '.$list[$i]['question'];
				echo '</div>';
				echo '<div class="faq_a" id="faq_'.$i.'" style="'.($list[$i]['faqId']!=$active?'display:none':'').'">';
					echo $list[$i]['answer'];
				echo '</div>';
			echo '</div>';	//class="faq_holder"
		}

	}
?>