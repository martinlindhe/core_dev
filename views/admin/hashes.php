<?php

namespace cd;

require_once('YuiDatatable.php');

$session->requireAdmin();

echo '<h1>Available hash functions</h1>';

$data = "The quick brown fox jumps over the lazy dog";

echo 'Hash calculated from string <b>'.$data.'</b><br/>';

$list = array();
foreach (hash_algos() as $h)
{
    $r = hash($h, $data);
    $list[] = array('algo' => $h, 'len' => strlen($r), 'hash' => $r);
}

$dt = new YuiDatatable();
$dt->addColumn('algo',     'Algo');
$dt->addColumn('len',      'Length');
$dt->addColumn('hash',     'Hash');
$dt->setRowsPerPage(50);
$dt->setDataSource( $list );
echo $dt->render();

?>
