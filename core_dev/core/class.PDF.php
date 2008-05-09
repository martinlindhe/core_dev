<?
define('FPDF_FONTPATH', $config['core']['fs_root'].'core/ext/fpdf_fonts/');
require($config['core']['fs_root'].'core/ext/fpdf.php');

class PDF extends FPDF
{
	function Header()
	{
		//Arial bold 15
		$this->SetFont('Arial','B',15);

		//Calculate width of title and position
		$w = $this->GetStringWidth($this->title)+6;
		$this->SetX((210-$w)/2);

		//Colors of frame, background and text
		$this->SetDrawColor(0,80,180);
		$this->SetFillColor(230,230,0);
		$this->SetTextColor(220,50,50);

		//Thickness of frame (1 mm)
		$this->SetLineWidth(1);

		//Title
		$this->Cell($w,9,$this->title,1,1,'C',1);

		//Line break
		$this->Ln(10);
	}

	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);

		//Arial italic 8
		$this->SetFont('Arial','I',8);

		//Text color in gray
		$this->SetTextColor(128);

		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}

	function ChapterTitle($num, $label)
	{
		//Arial 12
		$this->SetFont('Arial','',12);
		//Background color
		$this->SetFillColor(200,220,255);
		//Title
		$this->Cell(0,6,"Chapter $num : $label",0,1,'L',1);
		//Line break
		$this->Ln(4);
	}

	function ChapterBody($txt)
	{
		//Times 12
		$this->SetFont('Times','',12);
		//Output justified text
		$this->MultiCell(0,5,$txt);
		//Line break
		$this->Ln();

		//Mention in italics
		//$this->SetFont('','I');
		//$this->Cell(0,5,'(end of excerpt)');
	}

	function PrintChapter($num,$title,$txt)
	{
		$this->AddPage();
		$this->ChapterTitle($num,$title);
		$this->ChapterBody($txt);
	}

	//Colored table
	function FancyTable($header,$data)
	{
		//Colors, line width and bold font
		$this->SetFillColor(255,0,0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		//Header
		$w=array(20,40,20,25, 30,60,25,50);
		for($i=0; $i<count($header); $i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',1);
		$this->Ln();

		//Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');

		//Data
		$fill=0;
		foreach($data as $row) {
			$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
			$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
			$this->Cell($w[2],6,$row[2],'LR',0,'R',$fill);
			$this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);

			$this->Cell($w[4],6,$row[4],'LR',0,'R',$fill);
			$this->Cell($w[5],6,$row[5],'LR',0,'R',$fill);
			$this->Cell($w[6],6,$row[6],'LR',0,'R',$fill);
			$this->Cell($w[7],6,$row[7],'LR',0,'R',$fill);
			$this->Ln();
			$fill=!$fill;
		}
		$this->Cell(array_sum($w),0,'','T');
	}
}
?>
