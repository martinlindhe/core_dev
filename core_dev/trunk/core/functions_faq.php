<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Adds a new FAQ entry
 *
 * @param $_q question
 * @param $_a answer
 * @return FAQ id
 */
function addFAQ($_q, $_a)
{
	global $h, $db;
	if (!$h->session->isAdmin) return;

	$q = 'INSERT INTO tblFAQ SET question="'.$db->escape($_q).'",answer="'.$db->escape($_a).'",createdBy='.$h->session->id.',timeCreated=NOW()';
	return $db->insert($q);
}

/**
 * Updates a FAQ entry
 *
 * @param $_id FAQ id
 * @param $_q question
 * @param $_a answe
 * @return true on success
 */
function updateFAQ($_id, $_q, $_a)
{
	global $h, $db;
	if (!$h->session->isAdmin || !is_numeric($_id)) return false;

	$q = 'UPDATE tblFAQ SET question="'.$db->escape($_q).'",answer="'.$db->escape($_a).'" WHERE faqId='.$_id;
	$db->update($q);
    return true;
}

/**
 * Deletes a FAQ entry
 *
 * @param $_id FAQ id
 * @return true on success
 */
function deleteFAQ($_id)
{
	global $h, $db;
	if (!$h->session->isAdmin || !is_numeric($_id)) return false;

	$q = 'DELETE FROM tblFAQ WHERE faqId='.$_id;
	$db->delete($q);
    return true;
}

/**
 * Fetches all FAQ entries
 */
function getFAQ()
{
	global $db;

	$q = 'SELECT * FROM tblFAQ';
	return $db->getArray($q);
}

/**
 * Shows all FAQ entries with ability to edit them
 */
function showFAQ()
{
	global $h;

	$active = 0;

	if ($h->session->isAdmin) {
		if (!empty($_POST['faq_q']) && isset($_POST['faq_a'])) {
			$active = addFAQ($_POST['faq_q'], $_POST['faq_a']);
		}

		if (isset($_GET['fid']) && is_numeric($_GET['fid']) && isset($_POST['faq_uq']) && isset($_POST['faq_ua'])) {
			updateFAQ($_GET['fid'], $_POST['faq_uq'], $_POST['faq_ua']);
			$active = $_GET['fid'];
		}

		if (isset($_GET['fdel'])) deleteFAQ($_GET['fdel']);
	}

	$list = getFAQ();
	if (!$list && !$h->session->isAdmin) return;

	if (!$active && $list) $active = $list[0]['faqId'];

	//FAQ full Q&A details
	for ($i=0; $i<count($list); $i++) {
		echo '<div class="faq_holder" id="faq_holder_'.$i.'">';
			echo '<div class="faq_q" onclick="faq_focus('.$i.')">';
				echo ($i+1).'. '.$list[$i]['question'];
			echo '</div>';
			echo '<div class="faq_a" id="faq_'.$i.'" style="'.($list[$i]['faqId']!=$active?'display:none':'').'">';
				echo $list[$i]['answer'];

				if ($h->session->isAdmin) {
					echo '<br/><br/>';
					echo '<input type="button" class="button" value="'.t('Edit').'" onclick="faq_focus('.$i.'); hide_element(\'faq_holder_'.$i.'\'); show_element(\'faq_edit_'.$i.'\');"/> ';
					echo '<input type="button" class="button" value="'.t('Delete').'" onclick="document.location=\'?fdel='.$list[$i]['faqId'].'\'"/>';
				}

			echo '</div>';

		echo '</div>';	//id="faq_holder_x"

		if ($h->session->isAdmin) {
			echo '<div class="faq_holder" id="faq_edit_'.$i.'" style="display: none;">';
				echo '<form method="post" action="?fid='.$list[$i]['faqId'].'">';
				echo '<div class="faq_q">';
					echo ($i+1).'. <input type="text" name="faq_uq" size="40" value="'.$list[$i]['question'].'"/>';
				echo '</div>';
				echo '<div class="faq_a">';
					echo '<textarea rows="14" cols="60" name="faq_ua">'.$list[$i]['answer'].'</textarea><br/><br/>';
					echo '<input type="submit" class="button" value="'.t('Save').'"/>';
				echo '</div>';
				echo '</form>';
			echo '</div>';	//id="faq_edit_x"
		}
	}

	if ($h->session->isAdmin) {
		echo '<br/>';
		echo '<form method="post" action="">';
		echo t('Add new FAQ').': <input type="text" name="faq_q" size="40"/><br/>';
		echo t('Answer').':<br/>';
		echo '<textarea name="faq_a" rows="8" cols="60"></textarea><br/>';
		echo '<input type="submit" class="button" value="'.t('Add').'"/>';
		echo '</form>';
	}
}
?>
