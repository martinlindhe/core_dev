<?php

$session->requireAdmin();

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
//Toggles element with name "n" between visible and hidden
'function toggle_element(n)'.
'{'.
    'var e = document.getElementById(n);'.
    'e.style.display = (e.style.display?"":"none");'.
'}'
);

$answer_fields = 8;

if (!empty($_GET['poll_edit']) && is_numeric($_GET['poll_edit'])) {
    $pollId = $_GET['poll_edit'];

    if (!empty($_POST['poll_q'])) {
        $caller::updatePoll($pollId, $_POST['poll_q']);

        if (!empty($_POST['poll_ts'])) {
            $caller::updatePoll($pollId, $_POST['poll_q'], $_POST['poll_ts']);
        }
        if (!empty($_POST['poll_te'])) {
            $caller::updatePoll($pollId, $_POST['poll_q'], '', $_POST['poll_te']);
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

    if (isset($_GET['delete']) && confirmed('Are you sure you want to delete this site poll?') ) {
        $caller::removePoll($pollId);
        return;
    }

    $poll = $caller::getPoll($pollId);

    if (!empty($_GET['poll_stats'])) {
        echo '<h1>Poll stats</h1>';

        $votes = getPollStats($pollId);
        $tot_votes = 0;
        foreach ($votes as $row) $tot_votes += $row['cnt'];

        foreach ($votes as $row) {
            $pct = 0;
            if ($tot_votes) $pct = (($row['cnt'] / $tot_votes)*100);
            echo $row['categoryName'].' got '.$row['cnt'].' ('.$pct.'%) votes<br/>';
        }
        return;
    }

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
        $cats->setOwner($pollId);

        foreach ($cats->getItems() as $i => $opt)
            echo 'Answer '.($i+1).': <input type="text" size="30" name="poll_a'.$i.'" value="'.$opt->title.'"/><br/>';

        echo 'Add new answer: '.xhtmlInput('poll_new_a', '', 30).'<br/>';
    }

    echo xhtmlSubmit('Save changes');
    echo '</form><br/>';

//    echo '<a href="'.URLadd('poll_stats', $pollId).'">Poll stats</a><br/>';
    echo '<a href="?poll_edit='.$pollId.'&delete">Delete poll</a><br/>';

    return;
}

$_owner = 0;

if (!empty($_POST['poll_q'])) {
    if (!empty($_POST['poll_start_man'])) {
        $pollId = self::addPollExactPeriod($_owner, $_POST['poll_q'], $_POST['poll_start_man'], $_POST['poll_end_man']);
    } else {
        $pollId = self::addPoll($_owner, $_POST['poll_q'], $_POST['poll_dur'], $_POST['poll_start']);
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

echo '<h2 onclick="toggle_element(\'new_poll_form\')">Add new poll</h2>';
echo '<div id="new_poll_form" style="display: none;">';
echo '<form method="post" action="">';
echo 'Question: '.xhtmlInput('poll_q', '', 30).'<br/>';

echo '<div id="poll_period_selector">';
echo 'Duration of the poll: ';
echo '<select name="poll_dur">';
echo '<option value="day">1 day</option>';
echo '<option value="week" selected="selected">1 week</option>';
echo '<option value="month">1 month</option>';
echo '</select><br/>';

echo 'Poll start: ';
echo '<select name="poll_start">';
echo '<option value="thismonday">monday this week</option>';
echo '<option value="nextmonday">monday next week</option>';
echo '<option value="nextfree"'.(count($list)?' selected="selected"':'').'>next free time</option>';
echo '</select><br/>';
echo '<a href="#" onclick="hide_element(\'poll_period_selector\');show_element(\'poll_period_manual\')">Enter dates manually</a>';
echo '</div>';
echo '<div id="poll_period_manual" style="display: none;">';
    echo 'Start time: '.xhtmlInput('poll_start_man').' (format YYYY-MM-DD HH:MM)<br/>';
    echo 'End time: '.xhtmlInput('poll_end_man').'<br/>';
    echo '<a href="#" onclick="hide_element(\'poll_period_manual\');show_element(\'poll_period_selector\')">Use dropdown menus instead</a>';
echo '</div>';
echo '<br/><br/>';

for ($i=1; $i<=$answer_fields; $i++)
    echo t('Answer').' '.$i.': '.xhtmlInput('poll_a'.$i, '', 30).'<br/>';

echo xhtmlSubmit('Create');
echo '</form>';
echo '</div>';

echo '<h1>polls</h1>';

$list = $caller::getPolls();
if (count($list)) {
    echo '<table>';
    echo '<tr>';
    echo '<th>Title</th>';
    echo '<th>Starts</th>';
    echo '<th>Ends</th>';
    echo '</tr>';
}

foreach ($list as $row)
{
    $expired = $active = false;

    if (time()  > ts($row['timeEnd']))
        $expired = true;

    if (time() >= ts($row['timeStart']) && !$expired)
        $active = true;

    if ($expired) {
        echo '<tr style="font-style: italic">';
    } else if ($active) {
        echo '<tr style="font-weight: bold">';
    } else {
        echo '<tr>';
    }

    echo '<td><a href="?poll_edit='. $row['pollId'].'">'.$row['pollText'].'</a></td>';

    echo '<td>'.$row['timeStart'].'</td>';
    echo '<td>'.$row['timeEnd'].'</td>';

    echo '</tr>';
}

if (count($list)) echo '</table>';
echo '<br/>';

?>
