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

    public static function renderText($text)
    {
        do {
            $p1 = strpos($text, '[[');
            $p2 = strpos($text, ']]');
            if ($p1 === false || $p2 === false) break;

            $cmd = substr($text, $p1+strlen('[['), $p2-$p1-strlen(']]'));

            if (strpos($cmd, '|') !== false) {
                // [[Article name|headline for article]] format
                list($article, $title) = explode('|', $cmd);
                $result = ahref('u/wiki/show/'.self::formatWikiLink($article), $title);
            } else {
                // [[Article name]] format
                $result = ahref('u/wiki/show/'.self::formatWikiLink($cmd), $cmd);
            }

            $text = substr($text, 0, $p1) . $result . substr($text, $p2+strlen(']]'));
        } while (1);

        return $text;
    }

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

        return self::renderText($wiki->text);
    }

}
