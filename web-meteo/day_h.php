<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
require_once('db/connect.php');

if(isset($_GET['date'])){
	$date =  $_GET['date'];
	$curdate = "date_format(curdate,'%d/%m/%Y')";

		for ($i=0; $i < 24; $i++) { 

			$start = strval($i).":00:00";
			$stop = strval($i+1).":00:00";
			$query = $db -> prepare("SELECT date_format(curdate,'%d/%m/%Y'),
									 AVG(temperature) as temp,AVG(humidity) as hum,AVG(atm_pressure) as atm 
									 FROM weather WHERE curdate = '$date' 
									 AND curtime > '$start' AND curtime <= '$stop' ");
			$query -> execute();
			$result = $query -> fetch(PDO::FETCH_ASSOC);
  			
  			if ($result['temp'] != "") {
  				$tempe = sprintf('%0.2f',$result['temp']);
  				$humi = sprintf('%0.2f',$result['hum']);
  				$atmo = sprintf('%0.2f',$result['atm']);

  				$temp[$i] = $tempe;
  				$hum[$i] = $humi;
  				$atm[$i] = $atmo;
  				

  			}  else{
  				$temp[$i] = 0;
  				$hum[$i] = 0;
  				$atm[$i] = 0;
  				
  			}
  		
  	}
}

for ($i=0; $i < 24 ; $i++) { 
	$c[$i] = $i;
	
}

$j=0;

for ($i=0; $i < 24 ; $i++) { 
	$c[$i] = $i;
	$t[$i] = floatval($hum[$i]);
	if ($hum[$i]!=0) {
		$hum_d[$j] = floatval($hum[$i]);
		$j++;
	}
	if ($t[$i]==0) {
		$t[$i]="";
	}
}

$tex = "ΕΛΑΧΙΣΤΗ: ". min($hum_d) . "       ΜΕΣΗ: " . sprintf('%0.2f',array_sum($hum_d)/count($hum_d)) . "       ΜΕΓΙΣΤΗ: " . max($hum_d);


$min_h = (int)min($hum_d);
$max_h = (int)max($hum_d);

if ($min_h == -1) {
	$min_h = 0;
}



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
$p3 = new LinePlot($t);
$graph->Add($p3);
$p3->SetColor("#6495ED");
$p3->mark->SetType(MARK_DIAMOND);
$p3->mark->SetColor('#6495ED');
$p3->mark->SetFillColor('#6495ED');
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