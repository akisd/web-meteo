<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
require_once('db/connect.php');

if(isset($_GET['month'])){
	$nam = substr($_GET['month'],5,2);
	$nam = sprintf("%02d",$nam);
	$ye =  substr($_GET['month'],0,4);
	$month = $ye.'-'.$nam;

	$query = $db -> prepare("SELECT DISTINCT curdate FROM weather WHERE curdate LIKE '$month-%'");
	$query -> execute();
	
	$i=0;
	$d=1;
	while($days = $query -> fetch(PDO::FETCH_ASSOC)){
		$num_day[$d] = substr($days['curdate'], 8);
		$d++;
		$day = $days['curdate'];

			$query2 = $db -> prepare("SELECT AVG(atm_pressure) as atm FROM weather WHERE curdate = '$day'");
			$query2 -> execute();
			$result = $query2 -> fetch(PDO::FETCH_ASSOC);
  			
  			if ($result['atm'] != "") {
  				
  				$atmo = sprintf('%0.2f',$result['atm']);

  				$atm[$i] = $atmo;
  				$i++;

  			}  
  		
  	}
}

$conum = count($num_day);
$max_day = max($num_day);
$min_a = intval(min($atm))-1;
$max_a = intval(max($atm))+1;

$t = "ΕΛΑΧΙΣΤΗ: ". min($atm) . "       ΜΕΣΗ: " . sprintf('%0.2f',array_sum($atm)/count($atm)) . "       ΜΕΓΙΣΤΗ: " . max($atm);

if ($max_day <=30) {
	$pointer = 30;
}elseif ($nam == 02){
	$pointer = 28;
}else{
	$pointer = 31;
}



for ($i=0; $i < $pointer ; $i++) { 
	$c[$i] = $i+1;
	$a[$i] = "";
}

for ($i=1; $i <= $conum ; $i++) { 
	$p = intval($num_day[$i]);
	$a[$p-1] = floatval($atm[$i-1]);
}


// Setup the graph
$graph = new Graph(700,250);
$graph->SetScale("textlin",$min_a-10,$max_a+10);


$theme_class=new UniversalTheme;

$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
$graph->SetBox(false);

$graph->img->SetAntiAliasing();

$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
$graph->xaxis->SetTickLabels($c);
$graph->xgrid->SetColor('#E3E3E3');

// Create the first line
$p3 = new LinePlot($a);
$graph->Add($p3);
$p3->SetColor("#131493");
$p3->mark->SetType(MARK_DIAMOND);
$p3->mark->SetColor('#131493');
$p3->mark->SetFillColor('#131493');
//$p1->SetLegend('Line 1');
$txt = new Text();
$txt->SetFont(FF_ARIAL,FS_NORMAL,9);
$txt->Set($t);
$txt->SetParagraphAlign('center');
$txt->SetPos(0.5,0.03,'center');

$graph->Add($txt); 

$graph->legend->SetFrameWeight(1);

// Output line
$graph->Stroke();

?>