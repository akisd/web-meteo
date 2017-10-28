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
  				$humi = sprintf('%0.2f',$result['hum']);
  				$atmo = sprintf('%0.2f',$result['atm']);

  				$temp[$i] = $tempe;
  				$hum[$i] = $humi;
  				$atm[$i] = $atmo;
  				$i++;

  			}  
  		
  	}
}

$conum = count($num_day);
$max_day = max($num_day);
$min_t = (int)min($temp);
$max_t = (int)max($temp);

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
	$h[$i] = "";
	$a[$i] = "";
}

for ($i=1; $i <= $conum ; $i++) { 
	$p = intval($num_day[$i]);
	$t[$p-1] = floatval($temp[$i-1]);
	$h[$p-1] = floatval($hum[$i-1]);
	$a[$p-1] = floatval($atm[$i-1]);

}


// Setup the graph
$graph = new Graph(700,250);
$graph->SetScale("textdomain(text_domain)lin",$min_t-2,$max_t+2);


$theme_class=new UniversalTheme;

$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
$graph->title->Set('Filled Y-grid');
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
$p1->mark->SetColor('#6495ED');
$p1->mark->SetFillColor('#6495ED');
$p1->SetColor("#6495ED");
$p1->SetLegend('Line 1');

// Create the second line
$p2 = new LinePlot($h);
$graph->Add($p2);
$p2->SetColor("#B22222");
$p2->mark->SetType(MARK_DIAMOND);
$p2->mark->SetColor('#B22222');
$p2->mark->SetFillColor('#B22222');
$p2->SetLegend('Line 2');

// Create the third line
$p3 = new LinePlot($a);
$graph->Add($p3);
$p3->SetColor("#FF1493");
$p3->SetLegend('Line 3');

$graph->legend->SetFrameWeight(1);

// Output line
$graph->Stroke();














?>