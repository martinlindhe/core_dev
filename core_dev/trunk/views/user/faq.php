<?php

require_once('FaqItem.php');

$active = 0;

$list = FaqItem::getAll();

// auto focus on first entry in list
if (!$active && $list)
    $active = $list[0]->id;

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
        'show_el("faq_holder_"+i);'.
        'hide_el("faq_"+i);'.
    '}'.
    'show_el("faq_"+n);'.
'}'
);

// FAQ full Q&A details
foreach ($list as $i => $faq) {
    echo '<div class="faq_holder" id="faq_holder_'.$i.'">';
    echo '<div class="faq_q" onclick="faq_focus('.$i.')">';
        echo ($i+1).'. '.$faq->question;
    echo '</div>';
    echo '<div class="faq_a" id="faq_'.$i.'" style="'.($faq->id != $active ? 'display:none' : '').'">';
        echo $faq->answer;
    echo '</div>';
    echo '</div>'; // id="faq_holder_x"
}

if ($session->isAdmin) {
    echo '<br/>';
    echo '&raquo; '.ahref('a/faq', 'Administrera FAQ');
}

?>
