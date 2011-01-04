<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

require_once('atom_revisions.php');

//STATUS: wip - mostly working

//TODO: reimplement locking
//TODO: reimplement file attachments

class Wiki
{
    private $id, $name, $text;
    private $editorId, $lockerId;
    private $timestamp, $timeLocked;

    var $allow_edit  = false;  ///< false = only allow admins to edit the wiki articles. true = allow all, even anonymous

    function getName() { return $this->name; }
    function getId() { return $this->id; }
    function getText() { return $this->text; }
    function getTabs() { return $this->tabs; }

    function __construct($name = '')
    {
        $this->load($name);

        $header = XhtmlHeader::getInstance();
        $header->embedCss(
        '.wiki {'.
        ' font-size: 14px;'.
        '}'.
        '.wiki_body {'.
        ' padding: 10px;'.
        ' background-color: #F0F0F0;'.
        ' color: #000;'.
        ' border-width: 1px;'.
        ' border-left-style: solid;'.
        ' border-right-style: solid;'.
        ' border-bottom-style: solid;'.
        ' border-top-style: solid;'.
        '}'.
        '.wiki_locked {'.
        ' padding: 5px;'.
        ' padding-left: 25px;'.
        ' font-size: 20px;'.
        ' background: #ee99aa url("../../gfx/icon_locked.png") no-repeat;'.
        ' background-position: 5px 50%;'.
        '}'.
        '.wiki_menu {'.
        ' font-size: 12px;'.
        ' margin-top: 0;'.
        ' padding-left: 0;'.
        '}'.
        '.wiki_menu li {'.
        ' margin-left: 2px;'.
        ' margin-right: 2px;'.
        ' display: inline;'.
        ' border: 1px #000 solid;'.
        ' background-color: #ddd;'.
        ' padding: 4px;'.
        '}'.
        '.wiki_menu li a {'.
        ' color: #000;'.
        ' text-decoration: none;'.
        '}'.
        '.wiki_menu li:hover {'.
        ' background-color: #fff;'.
        '}');
    }

    private function load($name)
    {
        if (!$name) return false;
        $this->name = $name;

        $db = SqlHandler::getInstance();

        $q =
        'SELECT * FROM tblWiki AS t1'.
        ' WHERE wikiName="'.$db->escape($name).'"';
        $data = $db->getOneRow($q);
        if (!$data) return false;

        $this->id         = $data['wikiId'];
        $this->text       = $data['msg'];
        $this->editorId   = $data['editorId'];
        $this->lockerId   = $data['lockerId'];
        $this->timestamp  = $data['timeSaved'];
        $this->timeLocked = $data['timeLocked'];
        return true;
    }

    /** encodes a wiki link to web representation */
    private function formatWikiLink($s)
    {
        $tbl = array(
        ' ' => '_',          // "Install Guide" => "Install_Guide"
        );

        while (list($ord, $enc) = each($tbl))
            $s = str_replace($ord, $enc, $s);

        return $s;
    }

    /**
     * Formats text for wiki output
     */
    protected function formatWiki()
    {
        $session = SessionHandler::getInstance();

        if (empty($this->text)) {
            $res = t('The wiki').' "'.$this->name.'" '.t('does not yet exist').'!<br/>';
            if ($session->id && $session->isWebmaster)
                $res .= coreButton('Create', relurl('wiki/edit/'.$this->name));

            return $res;
        }

        $text = $this->text;

        do {
            $p1 = strpos($text, '[[');
            $p2 = strpos($text, ']]');
            if ($p1 === false || $p2 === false) break;

            $cmd = substr($text, $p1+strlen('[['), $p2-$p1-strlen(']]'));

            if (strpos($cmd, '|') !== false) {
                // [[Article|headline for article]] format
                list($article, $title) = explode('|', $cmd);
                $result = '<a href="/wiki/show/'.$this->formatWikiLink($article).'">'.$title.'</a>';
            } else {
                // [[Article]] format
                $result = '<a href="/wiki/show/'.$this->formatWikiLink($cmd).'">'.$cmd.'</a>';
            }

            $text = substr($text, 0, $p1) . $result . substr($text, $p2+strlen(']]'));
        } while (1);

        return $text;
    }

    function render()
    {
        if (!$this->name) return;

        $session = SessionHandler::getInstance();

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

        $menu = new XhtmlMenu();
        $menu->setCss('wiki_menu');
        $menu->add(t('Article'), '/wiki/show/'.$this->name);

        if ($session->id)
            $menu->add(t('Edit'),    '/wiki/edit/'.$this->name);

        $menu->add(t('History'), '/wiki/history/'.$this->name);

        echo '<div class="wiki">';
        echo $menu->render();
        echo '<div class="wiki_body">';
        echo $this->formatWiki();
        echo '</div>';
        echo '</div>';
    }

    function renderEdit()
    {
        function editWikiSubmit($p)
        {
            if (!isset($p['wiki_name']))
                return false;

            $db = SqlHandler::getInstance();
            $session = SessionHandler::getInstance();

            $text = trim($p['text']);

            $name = normalizeString($p['wiki_name'], array("\t"));

            $q = 'SELECT * FROM tblWiki WHERE wikiName="'.$db->escape($name).'"';
            $data = $db->getOneRow($q);

            //abort if we are trying to save a exact copy as the last one
            if ($data && $data['msg'] == $text)
                return false;

            if (!empty($data) && $data['wikiId']) {
                addRevision(REVISIONS_WIKI, $data['wikiId'], $data['msg'], $data['timeSaved'], $data['editorId'], REV_CAT_TEXT_CHANGED);

                $q = 'UPDATE tblWiki SET msg="'.$db->escape($p['text']).'",editorId='.$session->id.',revision=revision+1,timeSaved=NOW() WHERE wikiName="'.$db->escape($name).'"';
                $db->update($q);
                return true;
            }
            $q = 'INSERT INTO tblWiki SET wikiName="'.$db->escape($name).'",msg="'.$db->escape($p['text']).'",editorId='.$session->id.',revision=1,timeSaved=NOW()';
            $db->insert($q);
            return true;
        }

        $session = SessionHandler::getInstance();
        if (!$session->id)
            return false;

        $menu = new XhtmlMenu();
        $menu->setCss('wiki_menu');
        $menu->add(t('Article'), '/wiki/show/'.$this->name);
        $menu->add(t('Edit'),    '/wiki/edit/'.$this->name);
        $menu->add(t('History'), '/wiki/history/'.$this->name);

        echo '<div class="wiki">';
        echo $menu->render();

/*
        if (!$session->isAdmin && !$this->lockerId) {
            echo "WIKI LOCKED";
            return;
        }
*/
        $form = new XhtmlForm('wiki_edit');
        $form->addHidden('wiki_name', $this->name); ///XXXX ugly hack
        $form->addText('Edit wiki article '.$this->name);
/*
        if ($this->lockerId)
            echo '<div class="wiki_locked">This article is currently locked from editing.</div>';
*/
        $rows = 8+substr_count($this->text, "\n");
        if ($rows > 36) $rows = 36;

        $form->addRichedit('text', '', $this->text);

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
        if ($this->timestamp) {
            $editor = new User($this->editorId);
            echo formatTime($this->timestamp).' '.t('by').' '.$editor->getName();
        } else
            echo t('never');

        echo '</div>';
    }

    function renderHistory()
    {
        $menu = new XhtmlMenu();
        $menu->setCss('wiki_menu');
        $menu->add(t('Article'), '/wiki/show/'.$this->name);
        $menu->add(t('Edit'),    '/wiki/edit/'.$this->name);
        $menu->add(t('History'), '/wiki/history/'.$this->name);

        echo '<div class="wiki">';
        echo $menu->render();

        if ($this->text) {
            echo t('Current version').':<br/>';
            $editor = new User($this->editorId);
            echo '<b><a href="#" onclick="return toggle_element(\'layer_history_current\')">'.t('Edited').' '.formatTime($this->timestamp).' '.t('by').' '.$editor->getName().' ('.strlen($this->text).' '.t('characters').')</a></b><br/>';
            echo '<div id="layer_history_current" class="revision_entry">';
            echo nl2br(htmlentities($this->text, ENT_COMPAT, 'UTF-8'));
            echo '</div>';

            showRevisions(REVISIONS_WIKI, $this->id, $this->name);
        } else {
            echo 'There is no history for this wiki.';
        }

        echo '</div>';
    }
}
