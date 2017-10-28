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

			$query2 = $db -> prepare("SELECT AVG(temperature) as temp,AVG(humidity) as hum,AVG(atm_pressure) as atm 
									 FROM weather WHERE curdate = '$day'");
			$query2 -> execute();
			$result = $query2 -> fetch(PDO::FETCH_ASSOC);
  			
  			if ($result['temp'] != "") {
  				$tempe = sprintf('%0.2f',$result['temp']);
  				
  				$temp[$i] = $tempe;
  				
  				$i++;

  			}  
  		
  	}
}

$conum = count($num_day);
$max_day = max($num_day);
$min_t = (int)min($temp);
$max_t = (int)max($temp);

$tex = "ΕΛΑΧΙΣΤΗ: ". min($temp) . "       ΜΕΣΗ: " . sprintf('%0.2f',array_sum($temp)/count($temp))  . "       ΜΕΓΙΣΤΗ: " . max($temp);
 

if ($max_day <=30) {
	$pointer = 30;
}elseif ($nam == 02){
	$pointer = 28;
}else{
	$pointer = 31;
}



for ($i=0; $i < $pointer ; $i++) { 
	$c[$i] = $i+1;
	$t[$i] = "";
}

for ($i=1; $i <= $conum ; $i++) { 
	$p = intval($num_day[$i]);
	$t[$p-1] = floatval($temp[$i-1]);
	

}


// Setup the graph
$graph = new Graph(700,250);
$graph->SetScale("textlin",$min_t-2,$max_t+2); 


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
$p1 = new LinePlot($t);
$graph->Add($p1);
$p1->mark->SetType(MARK_DIAMOND);
$p1->mark->SetColor('#de3113');
$p1->mark->SetFillColor('#de3113');
$p1->SetColor("#de3113");
//$p1->SetLegend('Line 1');
$txt = new Text();
$txt->SetFont(FF_ARIAL,FS_NORMAL,9);
$txt->Set($tex);
$txt->SetParagraphAlign('center');
$txt->SetPos(0.5,0.03,'center');

$graph->Add($txt); 

$graph->legend->SetFrameWeight(1);

// Output line
$graph->Stroke();

?>