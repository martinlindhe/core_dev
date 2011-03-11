<?php

//STATUS: wip... half working

//TODO: rework ajax callback for vote
//TODO: move "get poll as csv" feature to PollManager

/** Shows one poll and lets the user interact with it */
class PollWidget
{
    function __construct($id = 0)
    {
        $this->id = $id;
    }

    static function getPoll($id)
    {
        $q = 'SELECT * FROM tblPolls WHERE pollId = ? AND deletedBy = 0';
        return SqlHandler::getInstance()->pSelectRow($q, 'i', $id);
    }

    static function getPolls($ownerId = 0)
    {
        $q =
        'SELECT * FROM tblPolls WHERE ownerId = ? AND deletedBy=0'.
        ' ORDER BY timeStart ASC,pollText ASC';
        return SqlHandler::getInstance()->pSelect($q, 'i', $ownerId);
    }

    /** Get statistics for specified poll */
    function getPollStats($id)
    {
        $q  =
        'SELECT t1.categoryName, '.
            '(SELECT COUNT(*) FROM tblPollVotes  WHERE voteId=t1.categoryId) AS cnt'.
        ' FROM tblCategories AS t1'.
        ' WHERE t1.ownerId = ?';
        return SqlHandler::getInstance()->pSelect($q, 'i', $id);
    }

    /** Has current user answered specified poll? */
    static function hasAnsweredPoll($id)
    {
        // XXX store & check by ip if anon?
        $session = SessionHandler::getInstance();
        if (!$session->id)
            return false;

        $q = 'SELECT pollId FROM tblPollVotes WHERE userId = ? AND pollId = ?';
        if (SqlHandler::getInstance()->pSelectItem($q, 'ii', $session->id, $id))
            return true;

        return false;
    }

    function render()
    {
        if (!$this->id)
            throw new Exception ('no id set');

        $data = self::getPoll($this->id);
        if (!$data)
            return false;

        $active = false;
        if (time() >= datetime_to_timestamp($data['timeStart']) && time() <= datetime_to_timestamp($data['timeEnd']))
            $active = true;

        if (!$data['timeStart'])
            $active = true;

        $header = XhtmlHeader::getInstance();

        $header->embedCss(
        '.poll_item{'.
            'background-color:#eee;'.
            'padding:5px;'.
            'cursor:pointer;'.
        '}'
        );

        $header->includeJs('core_dev/js/ajax.js');

        $header->embedJs(
        //Makes element with name "n" invisible in browser
        'function hide_element(n)'.
        '{'.
            'var e = document.getElementById(n);'.
            'e.style.display = "none";'.
        '}'.
        //Makes element with name "n" visible in browser
        'function show_element(n)'.
        '{'.
            'var e = document.getElementById(n);'.
            'e.style.display = "";'.
        '}'.

        //sends a ajax poll submit
        'function submit_poll(id,opt)'.
        '{'.
            'ajax_poll(id,opt);'.
            'hide_element("poll"+id);'.
            'show_element("poll_voted"+id);'.
        '}'.
        'function get_poll_csv(id)'.
        '{'.
            'var w = window.open("csv_poll.php?id="+id, "_blank");'.
            'w.focus();'.
        '}'.
        //Sends an AJAX call to submit someones vote for a site poll
        'var poll_request = null;'.
        'function ajax_poll(id,opt)'.   //XXXX requires core_dev/js/ajax.js   update to use yui or something
        '{'.
            'poll_request = new AJAX();'.
            'poll_request.GET("ajax_poll.php?i="+id+"&o="+opt, null);'.
        '}'
        );

        $session = SessionHandler::getInstance();

        $result = '<div class="item">';
        if ($active)
            $result .= t('Active poll').': ';

        $result .= $data['pollText'].'<br/><br/>';

        $result .= '<div id="poll'.$this->id.'">';
        if ($session->isAdmin && $data['timeStart'])
            $result .= t('Starts').': '.$data['timeStart'].', '.t('ends').' '.$data['timeEnd'].'<br/>';

        if ($session->id && $active && !self::hasAnsweredPoll($this->id)) {

            $cats = new CategoryList( CategoryItem::POLL_OPTIONS );
            $cats->setOwner($this->id);

            foreach ($cats->getItems() as $opt) {
                $result .=
                '<div class="poll_item" onclick="submit_poll('.$this->id.','.$opt->id.')">'.
                    $opt->title.
                '</div><br/>';
            }
        } else {
            if ($session->id) {
                $result .= '<br/>';
                if ($active) {
                    $result .= t('You already voted, showing current standings').':<br/><br/>';
                } else {
                    $result .= t('The poll closed, final result').':<br/><br/>';
                }
            }

            $votes = self::getPollStats($this->id);

            $tot_votes = 0;
            foreach ($votes as $row)
                $tot_votes += $row['cnt'];

            foreach ($votes as $row) {
                $pct = 0;
                if ($tot_votes) $pct = (($row['cnt'] / $tot_votes)*100);
                $result .= ' &bull; '.$row['categoryName'].' '.t('got').' '.$row['cnt'].' '.t('votes').' ('.$pct.'%)<br/>';
            }
        }

        if ($session->isAdmin)
            $result .= '<br/><input type="button" class="button" value="'.t('Save as .csv').'" onclick="get_poll_csv('.$this->id.')"/>';

        $result .= '</div>';

        if ($session->id) {
            $result .=
            '<div id="poll_voted'.$this->id.'" style="display:none">'.
                t('Your vote has been registered.').
            '</div>';
        }

        $result .= '</div>';    //class="item"

        return $result;
    }

}

?>
