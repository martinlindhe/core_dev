<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2013 <martin@startwars.org>
 */

//TODO: reimplement locking
//TODO: reimplement file attachments

namespace cd;

require_once('Wiki.php');

class WikiViewer extends Wiki
{
    /**
     * Encodes a wiki link to web representation
     */
    public static function formatWikiLink($s)
    {
        $tbl = array(
        ' ' => '_',          // "Install Guide" => "Install_Guide"
        );

        while (list($ord, $enc) = each($tbl))
            $s = str_replace($ord, $enc, $s);

        return $s;
    }

    /**
     *
     */
    public static function render($name)
    {
        $wiki = self::getByName($name);

        $session = SessionHandler::getInstance();

        if (empty($wiki->text))
        {
            $res = t('The wiki').' "'.$name.'" '.t('does not yet exist').'!<br/>';
            if ($session->isWebmaster) {
                $res .= ahref('u/wiki/edit/'.$name, 'Create').'<br/>';
            }

            return $res;
        }

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
        $menu->add(t('Article'), '/wiki/show/'.$wiki->name);

        if ($session->id)
            $menu->add(t('Edit'),    '/wiki/edit/'.$wiki->name);

        $menu->add(t('History'), '/wiki/history/'.$wiki->name);

        $res = '<div class="wiki">';
        $res .= $menu->render();
        $res .= '<div class="wiki_body">';


        $header = XhtmlHeader::getInstance();
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


        $text = $wiki->text;

        do {
            $p1 = strpos($text, '[[');
            $p2 = strpos($text, ']]');
            if ($p1 === false || $p2 === false) break;

            $cmd = substr($text, $p1+strlen('[['), $p2-$p1-strlen(']]'));

            if (strpos($cmd, '|') !== false) {
                // [[Article|headline for article]] format
                list($article, $title) = explode('|', $cmd);
                $result = '<a href="/wiki/show/'.self::formatWikiLink($article).'">'.$title.'</a>';
            } else {
                // [[Article]] format
                $result = '<a href="/wiki/show/'.self::formatWikiLink($cmd).'">'.$cmd.'</a>';
            }

            $text = substr($text, 0, $p1) . $result . substr($text, $p2+strlen(']]'));
        } while (1);


        $res .= '</div>';
        $res .= '</div>';

        return $text;
    }


    function renderHistory()
    {
        $menu = new XhtmlMenu();
        $menu->setCss('wiki_menu');
        $menu->add(t('Article'), 'u/wiki/show/'.$this->name);
        $menu->add(t('Edit'),    'u/wiki/edit/'.$this->name);
        $menu->add(t('History'), 'u/wiki/history/'.$this->name);

        echo '<div class="wiki">';
        echo $menu->render();

        if ($this->text) {
            echo t('Current version').':<br/>';

            echo
            '<b><a href="#" onclick="return toggle_element(\'layer_history_current\')">'.
            t('Edited').' '.formatTime($this->timestamp).' '.
            t('by').' '.User::get($this->editorId)->name.' ('.strlen($this->text).' '.
            t('characters').')</a></b><br/>';

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
