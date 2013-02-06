<?php

namespace cd;

require_once('Wiki.php');

switch ($this->owner) {
case 'show':
    // child = article name
    echo '<h2>Showing wiki article '.$this->child.'</h2>';
    echo WikiViewer::render($this->child);

    if ($session->id)
        echo ahref('u/wiki/edit/'.$this->child, 'Edit article');
    break;

case 'edit':
    // child = article name

    if (!$session->id)
        return false;

    function editWikiSubmit($p)
    {
        if (!isset($p['wiki_name']))
            return false;

        $session = SessionHandler::getInstance();

        $text = trim($p['text']);

        $name = normalizeString($p['wiki_name'], array("\t"));

        $wiki = Wiki::getByName($name);

        // abort if we are trying to save a exact copy as the last one
        if ($wiki->text == $text)
            return false;

        if ($wiki->id) {
            addRevision(REVISIONS_WIKI, $wiki->id, $wiki->text, $wiki->time_edited, $wiki->edited_by, REV_CAT_TEXT_CHANGED);

            $wiki->text = $p['text'];
            $wiki->edited_by = $session->id;
            $wiki->time_edited = sql_datetime( time() );
/*
            $q =
            'UPDATE '.self::$tbl_name.
            ' SET msg="'.$db->escape($p['text']).'",editorId='.$session->id.',revision=revision+1,timeSaved=NOW()'.
            ' WHERE wikiName="'.$db->escape($name).'"';
*/
//            $db->update($q);
            Wiki::store($wiki);
            return true;
        }

        $wiki->name = $name;
        $wiki->text = $p['text'];
        $wiki->edited_by = $session->id;
        $wiki->time_edited = sql_datetime( time() );
        Wiki::store($wiki);
/*
        $q =
        'INSERT INTO '.self::$tbl_name.
        ' SET wikiName="'.$db->escape($name).'",msg="'.$db->escape($p['text']).'",editorId='.$session->id.',revision=1,timeSaved=NOW()';
        $db->insert($q);
*/
        return true;
    }

    $wiki = Wiki::getByName($this->child);

    echo '<h2>Edit wiki '.$this->child.'</h2>';

    $menu = new XhtmlMenu();
    $menu->setCss('wiki_menu');
    $menu->add(t('Article'), 'u/wiki/show/'.$this->child);
    $menu->add(t('Edit'),    'u/wiki/edit/'.$this->child);
    $menu->add(t('History'), 'u/wiki/history/'.$this->child);

    echo '<div class="wiki">';
    echo $menu->render();

/*
    if (!$session->isAdmin && !$this->lockerId) {
        echo "WIKI LOCKED";
        return;
    }
*/
    $form = new XhtmlForm('wiki_edit');
    $form->addHidden('wiki_name', $this->child); ///XXXX ugly hack
    $form->addText('Edit wiki article '.$this->child);
/*
    if ($this->lockerId)
        echo '<div class="wiki_locked">This article is currently locked from editing.</div>';
*/
    $rows = 8+substr_count($wiki->text, "\n");
    if ($rows > 36) $rows = 36;

    $form->addRichedit('text', '', $wiki->text);

/*
    if ($session->isAdmin) {
        if ($this->lockerId) {
            echo '<input type="button" class="button" value="'.t('Unlock').'" onclick="location.href=\''.URLadd('WikiEdit:'.$this->name, '&amp;wikilock=0').'\'"/>';
            echo xhtmlImage('gfx/icon_locked.png', 'This wiki is currently locked');
            echo '<b>Locked by '.Users::getName($this->lockerId).' at '.formatTime($this->timeLocked).'</b><br/>';
        } else if ($this->text) {
            echo '<input type="button" class="button" value="'.t('Lock').'" onclick="location.href=\''.URLadd('WikiEdit:'.$this->name, '&amp;wikilock=1').'\'"/>';
            echo xhtmlImage('gfx/icon_unlocked.png', 'This article is open for edit by anyone');
        }
    }
*/

    $form->addSubmit('Save');
    $form->setHandler('editWikiSubmit');
    echo $form->render();

    echo t('Last edited').' ';
    if ($wiki->time_edited) {
        echo formatTime($wiki->time_edited).' '.t('by').' '.User::get($wiki->edited_by)->name;
    } else
        echo t('never');

    echo '</div>';



    break;

default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
