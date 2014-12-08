<?php

namespace cd;

$header->embedCss(
'.wiki {'.
    'font-size: 14px;'.
'}'.
'.wiki_body {'.
    'padding: 10px;'.
    'background-color: #F0F0F0;'.
    'color: #000;'.
    'border-width: 1px;'.
    'border-left-style: solid;'.
    'border-right-style: solid;'.
    'border-bottom-style: solid;'.
    'border-top-style: solid;'.
'}'.
'.wiki_locked {'.
    'padding: 5px;'.
    'padding-left: 25px;'.
    'font-size: 20px;'.
    'background: #ee99aa url("../../gfx/icon_locked.png") no-repeat;'.
    'background-position: 5px 50%;'.
'}'.
'.wiki_menu {'.
    'font-size: 12px;'.
    'margin-top: 0;'.
    'padding-left: 0;'.
'}'.
'.wiki_menu li {'.
    'margin-left: 2px;'.
    'margin-right: 2px;'.
    'display: inline;'.
    'border: 1px #000 solid;'.
    'background-color: #ddd;'.
    'padding: 4px;'.
'}'.
'.wiki_menu li a {'.
    'color: #000;'.
    'text-decoration: none;'.
'}'.
'.wiki_menu li:hover {'.
    'background-color: #fff;'.
'}');


switch ($this->owner) {
case 'show':
    // child = article name
    echo '<h2>Showing wiki article '.$this->child.'</h2>';

    $menu = new XhtmlMenu();
    $menu->setCss('wiki_menu');
    $menu->add(t('Article'), 'u/wiki/show/'.$this->child);

    if ($session->id)
        $menu->add(t('Edit'),    'u/wiki/edit/'.$this->child);

    $menu->add(t('History'), 'u/wiki/history/'.$this->child);

    echo $menu->render();

    echo
    '<div class="wiki">'.
    '<div class="wiki_body">'.
    WikiViewer::render($this->child).
    '</div>'.
    '</div>';
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

            $rev = new Revision();
            $rev->type         = WIKI;
            $rev->owner        = $wiki->id;
            $rev->value        = $wiki->text;
            $rev->time_created = $wiki->time_edited;
            $rev->created_by   = $wiki->edited_by;
            $rev->event        = EVENT_TEXT_CHANGED;
            $rev->id           = $rev->store();

            $wiki->text = $p['text'];
            $wiki->edited_by = $session->id;
            $wiki->time_edited = sql_datetime( time() );
            $wiki->revision++;
            $wiki->store();
            redir('u/wiki/show/'.$wiki->name);
        }

        $wiki->name = $name;
        $wiki->text = $p['text'];
        $wiki->edited_by = $session->id;
        $wiki->time_edited = sql_datetime( time() );
        $wiki->store();
        redir('u/wiki/show/'.$wiki->name);
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

/*
        if ($session->isAdmin && !empty($_GET['wikilock'])) {
            $q = 'UPDATE tblWiki SET lockerId='.$session->id.',timeLocked=NOW() WHERE wikiId='.$this->id;
            $db->update($q);
            $this->lockerId = $session->id;
            addRevision(REVISIONS_WIKI, $this->id, 'The wiki has been locked', now(), $session->id, REV_CAT_LOCKED);
        } else if ($session->isAdmin && isset($_GET['wikilock'])) {
            $q = 'UPDATE tblWiki SET lockerId=0 WHERE wikiId='.$this->id;
            $db->update($q);
            $this->lockerId = 0;
            addRevision(REVISIONS_WIKI, $this->id, 'The wiki has been unlocked', now(), $session->id, REV_CAT_UNLOCKED);
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

case 'history':
    // child = article name
    echo '<h2>History for wiki '.$this->child.'</h2>';

    $wiki = Wiki::getByName($this->child);

    $menu = new XhtmlMenu();
    $menu->setCss('wiki_menu');
    $menu->add(t('Article'), 'u/wiki/show/'.$this->child);
    $menu->add(t('Edit'),    'u/wiki/edit/'.$this->child);
    $menu->add(t('History'), 'u/wiki/history/'.$this->child);

    echo '<div class="wiki">';
    echo $menu->render();

    if ($wiki->id) {
        echo t('Current version').':<br/>';

        echo
        '<b><a href="#" onclick="return toggle_el(\'layer_history_current\')">'.
        t('Edited').' '.formatTime($wiki->time_edited).' '.
        t('by').' '.User::get($wiki->edited_by)->name.' ('.strlen($wiki->text).' '.
        t('characters').')</a></b><br/>';

        echo '<div id="layer_history_current" class="revision_entry">';
        echo nl2br(htmlentities($wiki->text, ENT_COMPAT, 'UTF-8'));
        echo '</div>';


        echo 'History of article '.$wiki->name.'<br/><br/>';

        $tot_cnt = Revision::getCount(WIKI, $wiki->id);

        $list = Revision::getAll(WIKI, $wiki->id);

        if (!$list) {
            echo '<b>There is no edit history of this wiki in the database.</b><br/>';
            return;
        }

        foreach ($list as $row) {
            echo formatTime($row->time_created).': ';

            $creator = User::get($row->created_by);
            switch ($row->event) {
            case EVENT_TEXT_CHANGED:
                echo '<a href="#" onclick="return toggle_el(\'layer_history'.$row->id.'\')">';
                echo t('Edited by').' '.$creator->name. ' ('.strlen($row->value).' '.t('characters').')</a><br/>';
                echo '<div id="layer_history'.$row->id.'" class="revision_entry" style="display: none;">';
                echo nl2br(htmlentities($row->value, ENT_COMPAT, 'UTF-8'));
                echo '</div>';
                break;

            case EVENT_LOCKED:
                echo '<img src="'.coredev_webroot().'gfx/icon_locked.png" width="16" height="16" alt="Locked"/>';
                echo ' Locked by '.$creator->name.'<br/>';
                break;

            case EVENT_UNLOCKED:
                echo '<img src="'.coredev_webroot().'gfx/icon_unlocked.png" width="16" height="16" alt="Unlocked"/>';
                echo ' Unlocked by '.$creator->name.'<br/>';
                break;

            case EVENT_FILE_UPLOADED:
                echo ' File uploaded by '.$creator->name.'<br/>';
                break;

            case EVENT_FILE_DELETED:
                echo ' File deleted by '.$creator->name.'<br/>';
                break;

            default:
                throw new \Exception ('unknown revision event '.$row->event);
            }
        }

    } else {
        echo 'There is no history for this wiki.';
    }

    echo '</div>';
    break;

default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
