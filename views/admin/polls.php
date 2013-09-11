<?php

//TODO later: ability to export results (csv, xls, ...?)

namespace cd;

require_once('PollItem.php');
require_once('YuiDatatable.php');

$session->requireAdmin();

if (!$this->owner)
    $this->owner = 'list';

switch ($this->owner) {
case 'list':
echo '<h1>Manage polls</h1>';
    $list = PollItem::getPolls(SITE);

    $dt = new YuiDatatable();
    $dt->addColumn('id',     '#', 'link', 'a/polls/edit/', 'text');
    $dt->addColumn('time_start', 'Starts');
    $dt->addColumn('time_end',   'Ends');
    $dt->addColumn('time_created', 'Created');
    $dt->setDataSource( $list );
    echo $dt->render();
    echo '<br/>';
    echo ahref('a/polls/add', 'Add new poll');
    break;

case 'edit':
    // child = poll id
    function editPoll($p)
    {
        if (empty($_POST['poll_q']))
            return;

        PollItem::updatePoll($p['poll'], $p['poll_q']);

        if (!empty($p['poll_ts'])) {
            PollItem::updatePoll($p['poll'], $p['poll_q'], $p['poll_ts']);
        }
        if (!empty($_POST['poll_te'])) {
            PollItem::updatePoll($p['poll'], $p['poll_q'], '', $p['poll_te']);
        }

        $cats = new CategoryList( POLL );
        $cats->setOwner($p['poll']);

        foreach ($cats->getItems() as $i => $opt)
            if (!empty($p['poll_a'.$i])) {
                $opt->title = $p['poll_a'.$i];
                $opt->store();
            }

        if (!empty($p['poll_new_a'])) {
            $item = new CategoryItem( POLL );
            $item->owner = $p['poll'];
            $item->title = $p['poll_new_a'];
            $item->store();
        }
        js_redirect('a/polls/edit/'.$p['poll']);
    }

    $poll = PollItem::get($this->child);

    echo '<h1>Edit poll</h1>';

    $frm = new XhtmlForm();
    $frm->addHidden('poll', $this->child);
    $frm->addInput('poll_q', 'Question', $poll->text);

    if (ts($poll->time_start) < time()) {
        $frm->addText($poll->time_start, 'Poll starts');
    } else {
        $frm->addInput('poll_ts', 'Poll starts', $poll->time_start);
    }

    if (ts($poll->time_end) < time()) {
        $frm->addText($poll->time_end, 'Poll ends');
    } else {
        $frm->addInput('poll_te', 'Poll ends', $poll->time_end);
    }

    if ($poll) {
        $cats = new CategoryList( POLL );
        $cats->setOwner($this->child);

        foreach ($cats->getItems() as $i => $opt)
            $frm->addInput('poll_a'.$i, 'Answer '.($i+1), $opt->title);

        $frm->addInput('poll_new_a', 'New answer');
    }

    $frm->addSubmit('Save');
    $frm->setHandler('editPoll');
    echo $frm->render();

    echo '<br/>';
    echo ahref('a/polls/stats/'.$this->child, 'Poll stats').'<br/>';
    echo '<br/>';
    echo ahref('a/polls/remove/'.$this->child, 'Remove poll').'<br/>';
    break;

case 'stats':
    echo '<h1>Poll stats</h1>';

    $votes = Rating::getStats(POLL, $this->child);

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
    break;

case 'remove':
    if (confirmed('Are you sure you want to remove this site poll?') ) {
        PollItem::removePoll($this->child);
        js_redirect('a/polls/list');
    }
    break;

case 'add':

    function addPoll($p)
    {
        if (empty($p['poll_q']))
            return;

        if (!empty($p['poll_start_man'])) {
            $pollId = PollItem::addPollExactPeriod(SITE, 0, $p['poll_q'], $p['poll_start_man'], $p['poll_end_man']);
        } else {
            $pollId = PollItem::add(SITE, 0, $p['poll_q'], $p['poll_dur'], $p['poll_start']);
        }

        for ($i=1; $i<=8; $i++) {
            if (empty($p['poll_a'.$i]))
                continue;

            $item = new CategoryItem(POLL);
            $item->owner = $pollId;
            $item->title = $p['poll_a'.$i];
            $item->store();
        }

        js_redirect('a/polls/list');
    }

    echo '<h2>Add new poll</h2>';

    $frm = new XhtmlForm();
    $frm->addInput('poll_q', 'Question');

//    echo '<div id="poll_period_selector">';

    $dur = array('1d'=>'1 day', '1w'=>'1 week','1m'=>'1 month');
    $frm->addDropdown('poll_dur', 'Duration', $dur, '1w');

    $start = array(
    'thismonday'=>'monday this week',
    'nextmonday'=>'monday next week',
    'nextfree'  =>'next free time'
    );
    $frm->addDropdown('poll_start', 'Starting', $start, 'nextmonday');

//    echo '<a href="#" onclick="hide_el(\'poll_period_selector\');show_el(\'poll_period_manual\')">Enter dates manually</a>';
//    echo '</div>';
//    echo '<div id="poll_period_manual" style="display: none;">';
//        echo 'Start time: '.xhtmlInput('poll_start_man').' (format YYYY-MM-DD HH:MM)<br/>';
//        echo 'End time: '.xhtmlInput('poll_end_man').'<br/>';
//        echo '<a href="#" onclick="hide_el(\'poll_period_manual\');show_el(\'poll_period_selector\')">Use dropdown menus instead</a>';
//    echo '</div>';
    echo '<br/><br/>';

    for ($i=1; $i<=8; $i++)
        $frm->addInput('poll_a'.$i,  'Answer '.$i);

    $frm->addSubmit('Create');
    $frm->setHandler('addPoll');
    echo $frm->render();
    break;

default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
