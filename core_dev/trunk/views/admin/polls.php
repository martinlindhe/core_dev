<?php

//TODO: fix ability to edit poll
//TODO: fix ability to add new poll


require_once('PollItem.php');
require_once('YuiDatatable.php');

$session->requireAdmin();

if (!$this->owner)
    $this->owner = 'list';

switch ($this->owner) {
case 'list':
echo '<h1>Polls</h1>';
    $list = PollItem::getPolls(SITE);

    $dt = new YuiDatatable();
    $dt->addColumn('pollId',     '#', 'link', 'a/polls/edit/', 'title');
    $dt->addColumn('timeStart', 'Starts');
    $dt->addColumn('timeEnd',   'Ends');
    $dt->addColumn('timeCreated', 'Created');
    $dt->setDataSource( $list );
    echo $dt->render();
    echo '<br/>';
    echo ahref('a/polls/add', 'Add new poll');
    break;

case 'edit':
    // child = poll id



/*
if (!empty($_GET['poll_edit']) && is_numeric($_GET['poll_edit'])) {
    $pollId = $_GET['poll_edit'];

    if (!empty($_POST['poll_q'])) {
        PollItem::updatePoll($pollId, $_POST['poll_q']);

        if (!empty($_POST['poll_ts'])) {
            PollItem::updatePoll($pollId, $_POST['poll_q'], $_POST['poll_ts']);
        }
        if (!empty($_POST['poll_te'])) {
            PollItem::updatePoll($pollId, $_POST['poll_q'], '', $_POST['poll_te']);
        }

        $cats = new CategoryList( CategoryItem::POLL_OPTIONS );
        $cats->setOwner($pollId);

        foreach ($cats->getItems() as $i => $opt)
            if (!empty($_POST['poll_a'.$i])) {
                $opt->title = $_POST['poll_a'.$i];
                $opt->store();
            }

        if (!empty($_POST['poll_new_a'])) {
            $item = new CategoryItem( CategoryItem::POLL_OPTIONS );
            $item->owner = $pollId;
            $item->title = $_POST['poll_new_a'];
            $item->store();
        }
    }
}
*/

    $poll = PollItem::get($this->child);

    echo '<h1>Edit poll</h1>';

    echo '<form method="post" action="">';
    echo 'Question: ';
    echo xhtmlInput('poll_q', $poll['pollText'], 30).'<br/>';

    echo 'Poll starts: ';
    if (datetime_to_timestamp($poll['timeStart']) < time()) {
        echo $poll['timeStart'].'<br/>';
    } else {
        echo xhtmlInput('poll_ts', $poll['timeStart'], 30).'<br/>';
    }
    echo 'Poll ends: ';

    if (datetime_to_timestamp($poll['timeEnd']) < time()) {
        echo $poll['timeEnd'].'<br/>';
    } else {
        echo xhtmlInput('poll_te', $poll['timeEnd'], 30).'<br/>';
    }
    echo '<br/>';

    if ($poll) {
        $cats = new CategoryList( CategoryItem::POLL_OPTIONS );
        $cats->setOwner($this->child);

        foreach ($cats->getItems() as $i => $opt)
            echo 'Answer '.($i+1).': <input type="text" size="30" name="poll_a'.$i.'" value="'.$opt->title.'"/><br/>';

        echo 'Add new answer: '.xhtmlInput('poll_new_a', '', 30).'<br/>';
    }

    echo xhtmlSubmit('Save changes');

    echo '<br/>';
    echo ahref('a/polls/stats/'.$this->child, 'Poll stats').'<br/>';
    echo '<br/>';
    echo ahref('a/polls/remove/'.$this->child, 'Remove poll').'<br/>';
    break;

case 'stats':
    echo '<h1>Poll stats</h1>';

    $votes = PollItem::getPollStats($this->child);
    $tot_votes = 0;
    foreach ($votes as $row)
        $tot_votes += $row['cnt'];

    foreach ($votes as $row) {
        $pct = 0;
        if ($tot_votes) $pct = (($row['cnt'] / $tot_votes)*100);
        echo $row['categoryName'].' got '.$row['cnt'].' ('.$pct.'%) votes<br/>';
    }
    break;

case 'remove':
    if (confirmed('Are you sure you want to remove this site poll?') ) {
        PollItem::removePoll($this->child);
    }
    break;

case 'add':
/*
if (!empty($_POST['poll_q'])) {
    if (!empty($_POST['poll_start_man'])) {
        $pollId = $caller::addPollExactPeriod(SITE, $owner, $_POST['poll_q'], $_POST['poll_start_man'], $_POST['poll_end_man']);
    } else {
        $pollId = PollItem::add(SITE, $owner, $_POST['poll_q'], $_POST['poll_dur'], $_POST['poll_start']);
    }

    for ($i=1; $i<=$answer_fields; $i++) {
        if (!empty($_POST['poll_a'.$i])) {
            $item = new CategoryItem( CategoryItem::POLL_OPTIONS );
            $item->owner = $pollId;
            $item->title = $_POST['poll_a'.$i];
            $item->store();
        }
    }
}
*/
    echo '<h2>Add new poll</h2>';

    echo '<form method="post" action="">';
    echo 'Question: '.xhtmlInput('poll_q', '', 30).'<br/>';

    echo '<div id="poll_period_selector">';
    echo 'Duration of the poll: ';
    echo '<select name="poll_dur">';
    echo '<option value="1d">1 day</option>';
    echo '<option value="1w" selected="selected">1 week</option>';
    echo '<option value="1m">1 month</option>';
    echo '</select><br/>';

    echo 'Poll start: ';
    echo '<select name="poll_start">';
    echo '<option value="thismonday">monday this week</option>';
    echo '<option value="nextmonday">monday next week</option>';
    echo '<option value="nextfree"'.(count($list)?' selected="selected"':'').'>next free time</option>';
    echo '</select><br/>';
    echo '<a href="#" onclick="hide_el(\'poll_period_selector\');show_el(\'poll_period_manual\')">Enter dates manually</a>';
    echo '</div>';
    echo '<div id="poll_period_manual" style="display: none;">';
        echo 'Start time: '.xhtmlInput('poll_start_man').' (format YYYY-MM-DD HH:MM)<br/>';
        echo 'End time: '.xhtmlInput('poll_end_man').'<br/>';
        echo '<a href="#" onclick="hide_el(\'poll_period_manual\');show_el(\'poll_period_selector\')">Use dropdown menus instead</a>';
    echo '</div>';
    echo '<br/><br/>';

    for ($i=1; $i<=8; $i++)
        echo 'Answer '.$i.': '.xhtmlInput('poll_a'.$i, '', 30).'<br/>';

    echo xhtmlSubmit('Create');
    echo '</form>';
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}



?>
