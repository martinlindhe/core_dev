<?php

//TODO later: allow anonymous polls? then allow 1 vote from each IP

require_once('PollItem.php');

switch ($this->owner) {
case 'active':
    echo '<h1>Active polls</h1>';
    $list = PollItem::getActivePolls(SITE);
//    d($list);
    foreach ($list as $p) {
        echo ahref('u/polls/show/'.$p->id, $p->text).'<br/>';
    }
    break;

case 'show':
    //child = poll id

    if (!$this->child)
        throw new Exception ('no id set');

    $poll = PollItem::get($this->child);
    if (!$poll)
        die('meh');

    if (!empty($_GET['poll_vote']) && !empty($_GET['opt']))
    {
        if (!$session->id || !is_numeric($_GET['poll_vote']) || !is_numeric($_GET['opt']))
            throw new Exception ('XXX');

        $page->disableDesign();

        PollItem::addVote($_GET['poll_vote'], $_GET['opt']);

        ob_clean(); // XXX hack.. removes previous output
        die('1');
    }

    $header = XhtmlHeader::getInstance();

    $header->embedCss(
    '.poll_item{'.
        'background-color:#eee;'.
        'padding:5px;'.
        'cursor:pointer;'.
    '}'
    );

    $header->includeJs('http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js');

    $header->embedJs(
    'function submit_poll(id,opt)'.
    '{'.
        'YUI().use("io-base", function(Y) {'.
            'var uri = "?poll_vote=" + id + "&opt=" + opt;'.

            // Define a function to handle the response data
            'function complete(id, o, args) {'.
                'var id = id;'.               // Transaction ID
                'var data = o.responseText;'. // Response data
                'var args = args[1];'.
                'if (data==1) return;'.
                'alert("Voting error " + data);'.
            '};'.

            // Subscribe to event "io:complete", and pass an array
            // as an argument to the event handler "complete", since
            // "complete" is global.   At this point in the transaction
            // lifecycle, success or failure is not yet known.
            'Y.on("io:complete", complete, Y, ["lorem", "ipsum"]);'.

            // Make request
            'var request = Y.io(uri);'.
        '});'.

        'hide_el("poll"+id);'.
        'show_el("poll_voted"+id);'.
    '}'
    );

    $active = false;
    if (time() >= ts($poll->time_start) && time() <= ts($poll->time_end))
        $active = true;

    if (!$poll->time_start)
        $active = true;

    echo '<div class="item">';
    if ($active)
        echo 'Active poll: ';

    echo $poll->text.'<br/><br/>';

    echo '<div id="poll'.$this->child.'">';
    if ($poll->time_start)
        echo 'Starts: '.$poll->time_start.', ends '.$poll->time_end.'<br/>';

    if ($session->id && $active && !PollItem::hasAnsweredPoll($this->child))
    {
        $cats = new CategoryList( POLL );
        $cats->setOwner($this->child);

        $list = $cats->getItems();

        if (!$list)
            echo '<div class="critical">No options is available to this poll!</div>';
        else if (count($list) == 1)
            echo '<div class="critical">Only one options is available to this poll!</div>';
        else
            foreach ($list as $opt)
                echo
                    '<div class="poll_item" onclick="submit_poll('.$this->child.','.$opt->id.')">'.
                    $opt->title.
                '</div><br/>';
    } else {
        if ($session->id) {
            echo '<br/>';
            if ($active) {
                echo 'You already voted, showing current standings:<br/><br/>';
            } else {
                echo 'The poll closed, final result:<br/><br/>';
            }
        }

        $votes = PollItem::getPollStats($this->child);

        $tot_votes = 0;
        foreach ($votes as $cnt)
            $tot_votes += $cnt;

        $list = array();
        foreach ($votes as $title => $cnt) {
            $pct = 0;
            if ($tot_votes)
                $pct = (($cnt / $tot_votes)*100);
            echo ' &bull; '.$title.' got '.$cnt.' votes ('.$pct.'%)<br/>';

            $list[] = array('name' => $title, 'value' => $cnt);
        }

        $pie = new Yui3PieChart();
        $pie->setWidth(100);
        $pie->setHeight(100);
        $pie->setCategoryKey('name');
        $pie->setDataSource($list);

        echo $pie->render();
    }

    echo '</div>';

    if ($session->id) {
    echo
        '<div id="poll_voted'.$this->child.'" style="display:none">'.
            'Your vote has been registered.'.
        '</div>';
    }

    echo '</div>';    //class="item"
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
