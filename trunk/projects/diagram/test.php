<?php

require('class.Diagram.php');


$d = new Diagram();

$d->VLine(0, 10000, 1000);	//min,max,step.	FIXME hmm... detta kanske ska räknas ut av datan...
$d->VText("Changes per release");

$d->HLine(11, 24, 1);		//FIXME kanske ska räknas ut...
$d->HText("Days of development");

$d->BGCol(200,200,155);
$d->TextCol(0, 0, 0);
$d->LineCol(255,255,255);

//$d->AddH(


$d->Size(500, 300); //pixels
$d->Display();

?>
