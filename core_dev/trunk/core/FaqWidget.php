<?php
/**
 * $Id$
 *
 * Shows all FAQ entries with ability to add/edit/remove for admins
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO later: detach admin editing to its own class & make a view using it and put in core_dev admin interface

class FaqWidget
{
    private static function getEntries()
    {
        $q = 'SELECT * FROM tblFAQ';
        return SqlHandler::getInstance()->pSelect($q);
    }

    private static function add($question, $answer)
    {
        $q = 'INSERT INTO tblFAQ SET question = ?, answer = ?, createdBy = ?, timeCreated=NOW()';
        return SqlHandler::getInstance()->pInsert($q, 'ssi', $question, $answer, SessionHandler::getInstance()->id);
    }

    private static function update($id, $question, $answer)
    {
        $q = 'UPDATE tblFAQ SET question = ?, answer = ? WHERE faqId = ?';
        return SqlHandler::getInstance()->pUpdate($q, 'ssi', $question, $answer, $id);
    }

    private static function delete($id)
    {
        $q = 'DELETE FROM tblFAQ WHERE faqId = ?';
        return SqlHandler::getInstance()->pDelete($q, 'i', $id);
    }

    function render()
    {
        $session = SessionHandler::getInstance();

        $active = 0;

        if ($session->isAdmin) {
            if (!empty($_POST['faq_q']) && isset($_POST['faq_a']))
                $active = self::add($_POST['faq_q'], $_POST['faq_a']);

            if (isset($_GET['fid']) && is_numeric($_GET['fid']) && isset($_POST['faq_uq']) && isset($_POST['faq_ua'])) {
                self::update($_GET['fid'], $_POST['faq_uq'], $_POST['faq_ua']);
                $active = $_GET['fid'];
            }

            if (isset($_GET['fdel']))
                self::delete($_GET['fdel']);
        }

        $list = self::getEntries();
        if (!$list && !$session->isAdmin)
            return;

        // auto focus on first entry in list
        if (!$active && $list)
            $active = $list[0]['faqId'];

        $header = XhtmlHeader::getInstance();

        $header->embedCss(
        '.faq_holder{'.
            'border:1px #888 solid;'.
            'background-color:#fff;'.
            'max-width:600px;'.
            'color:#444;'.
        '}'.
        '.faq_holder:hover{'.
            'background-color:#eee;'.
        '}'.
        '.faq_q{'.
            'font-size:20px;'.
            'font-weight:bold;'.
            'padding:10px;'.
            'cursor:pointer;'.
        '}'.
        '.faq_a{'.
            'padding:10px;'.
        '}'
        );

        $header->embedJs(
        //focuses on the faq item #i
        'function faq_focus(n)'.
        '{'.
            'for (i=0;i<'.(count($list)).';i++) {'.
                ($session->isAdmin ? 'hide_el("faq_edit_"+i);' : '').
                'show_el("faq_holder_"+i);'.
                'hide_el("faq_"+i);'.
            '}'.
            'show_el("faq_"+n);'.
        '}'
        );

        // FAQ full Q&A details
        foreach ($list as $i => $row) {
            echo '<div class="faq_holder" id="faq_holder_'.$i.'">';
                echo '<div class="faq_q" onclick="faq_focus('.$i.')">';
                    echo ($i+1).'. '.$row['question'];
                echo '</div>';
                echo '<div class="faq_a" id="faq_'.$i.'" style="'.($row['faqId'] != $active ? 'display:none' : '').'">';
                    echo $list[$i]['answer'];

                    if ($session->isAdmin) {
                        echo '<br/><br/>';
                        echo '<input type="button" class="button" value="'.t('Edit').'" onclick="faq_focus('.$i.'); hide_el(\'faq_holder_'.$i.'\'); show_el(\'faq_edit_'.$i.'\');"/> ';
                        echo '<input type="button" class="button" value="'.t('Delete').'" onclick="document.location=\'?fdel='.$row['faqId'].'\'"/>';
                    }

                echo '</div>';

            echo '</div>'; // id="faq_holder_x"

            if ($session->isAdmin) {
                echo '<div class="faq_holder" id="faq_edit_'.$i.'" style="display: none;">';
                    echo '<form method="post" action="?fid='.$row['faqId'].'">';
                    echo '<div class="faq_q">';
                        echo ($i+1).'. Edit <input type="text" name="faq_uq" size="40" value="'.$row['question'].'"/>';
                    echo '</div>';
                    echo '<div class="faq_a">';
                        echo '<textarea rows="14" cols="60" name="faq_ua">'.$row['answer'].'</textarea><br/><br/>';
                        echo '<input type="submit" class="button" value="'.t('Save').'"/>';
                    echo '</div>';
                    echo '</form>';
                echo '</div>'; // id="faq_edit_x"
            }
        }

        if ($session->isAdmin) {
            echo '<br/>';
            echo '<form method="post" action="">';
            echo t('Add new FAQ').': <input type="text" name="faq_q" size="40"/><br/>';
            echo t('Answer').':<br/>';
            echo '<textarea name="faq_a" rows="8" cols="60"></textarea><br/>';
            echo '<input type="submit" class="button" value="'.t('Add').'"/>';
            echo '</form>';
        }
    }

}

?>
