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

			$query2 = $db -> prepare("SELECT AVG(humidity) as hum FROM weather WHERE curdate = '$day'");
			$query2 -> execute();
			$result = $query2 -> fetch(PDO::FETCH_ASSOC);
  			
  			if ($result['hum'] != "") {
  				
  				$humi = sprintf('%0.2f',$result['hum']);

  				$hum[$i] = $humi;
  				$i++;

  			}  
  		
  	}
}

$conum = count($num_day);
$max_day = max($num_day);
$min_h = (int)min($hum);
$max_h = (int)max($hum);

if ($max_day <=30) {
	$pointer = 30;
}elseif ($nam == 02){
	$pointer = 28;
}else{
	$pointer = 31;
}



for ($i=0; $i < $pointer ; $i++) { 
	$c[$i] = $i+1;
	$h[$i] = "";
}

for ($i=1; $i <= $conum ; $i++) { 
	$p = intval($num_day[$i]);
	$h[$p-1] = floatval($hum[$i-1]);
}

$t = "ΕΛΑΧΙΣΤΗ: ". min($hum) . "       ΜΕΣΗ: " . sprintf('%0.2f',array_sum($hum)/count($hum)) . "       ΜΕΓΙΣΤΗ: " . max($hum);

// Setup the graph
$graph = new Graph(700,250);
$graph->SetScale("textlin",$min_h-2,$max_h+2);


$theme_class=new UniversalTheme;

$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
//$graph->title->Set('Filled Y-grid');
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
$p1 = new LinePlot($h);
$graph->Add($p1);
$p1->mark->SetType(MARK_DIAMOND);
$p1->mark->SetColor('#6495ED');
$p1->mark->SetFillColor('#6495ED');
$p1->SetColor("#6495ED");
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