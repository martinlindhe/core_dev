<?

require('class.Diagram.php');


$d = new Diagram();

$d->VLine(0, 4, 0.5);	//min,max,step
$d->VText("Changes per release");

$d->HLine(11, 24, 1);
$d->HText("Days of development");

$d->BGCol(200,200,155);
$d->TextCol(0, 0, 0);

$d->Size(500, 300); //pixels
$d->Display();

?>
