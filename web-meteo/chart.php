<?php
	require_once('db/connect.php');
	require_once ('jpgraph/src/jpgraph.php');
	require_once ('jpgraph/src/jpgraph_line.php');
	
	date_default_timezone_set("Europe/Athens");

	$month_name = array(
							"Ιανουάριος",
							"Φεβρουάριος",
							"Μάρτιος    ",
							"Απρίλιος   ",
							"Μάιος      ",
							"Ιούνιος    ",
							"Ιούλιος    ",
							"Αύγουστος  ",
							"Σεπτέμβριος",
							"Οκτώμβριος ",
							"Νοέμβριος  ",
							"Δεκέμβριος "
						);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link href="images/sun2.png" rel="icon" type="image/x-icon" />
	<title>arduino weather station</title>
	
</head>
<body>
	
	<div id='header'>
		<div class='wrapper'>
			<div id='logo'></div>
		</div>
	</div>
	<div id="main-container">
		<div class='wrapper'>
			<div id='navigator'>
				<ul>
					<li><a href="index.php"><img src="images/home.png"></a></li>
					<li><a href="list.php"><img src="images/list.png"></a></li>
					<li><a href="chart.php"><img src="images/chart.png"></a></li>
				</ul>	
			</div>
				<div id='list-container'>
				<div id="left-list">

<?php

	$query = $db -> prepare("SELECT DISTINCT date_format(curdate,'%d/%m/%Y') FROM weather");
	$query -> execute();

	$date_1 = $query -> fetchAll(PDO::FETCH_COLUMN, 0);
	$count = count($date_1);
	asort($date_1);

	for ($i=0; $i<$count; $i++){
		$my[]=substr($date_1[$i],3,7);
	}	
	
	$my = array_unique($my);
	foreach ($my as $key ) {
		$moye[] = $key;
	}
	
	$num_moye = count($moye);
	
	$query = $db -> prepare("SELECT DISTINCT curdate FROM weather");
	$query -> execute();

	$date_2 = $query -> fetchAll(PDO::FETCH_COLUMN, 0);
	
	

?>



				<form class="chartform" action='chart.php' method='get'>
						<ul>
							<li>	
								
										<select class="drop-menu" id="month" name="month" >

											<?php
													if (isset($_GET['month']) || isset($_GET['date'])) {
														if(!isset($_GET['month'])){
															$nam = substr($_GET['date'],5,2);
															$ye = substr($_GET['date'],0,4);
														}else{
															$nam = substr($_GET['month'],5);
															$ye= substr($_GET['month'],0,4);
														}
														echo "<option disabled selected>" .$month_name[$nam-1] . " " . $ye .
														"<?option>";
													}else{
														echo "<option disabled selected >EΠΙΛΟΓΗ ΜΗΝΑ<?option>";
													}

											?>
 
											
											<?php
												

												for ($i=0; $i < $num_moye; $i++) {
													$mon = substr(intval($moye[$i]),0,2);
													$year = substr($moye[$i],3,4);
													$k=1;
													if ($mon == 1 && $year != substr($moye[0],3,4)) {
														echo "<option disabled>------------------------</option>";
													}
													if (isset($_GET['month']) && $_GET['month'] == $mon){
														$selected = 'selected';
													}else{
														$selected = '';
													}	
													
													echo "<option $selected value='$year-$mon'>" . $month_name[$mon-1] . " " . $year . "</option>";
														
													}
											?>
										</select>
											
							</li>
							<li>
								<input  id='submit' value='ΕΠΙΛΟΓΗ' type='submit'> 
							</li>
						</ul>				
											
				</form>
				
					<?php

					if (isset($_GET['month']) || isset($_GET['date']) ) {

						if(!isset($_GET['date'])){
							$d = substr($_GET['month'],5,2);
							$y = substr($_GET['month'],0,4);
						}else{
							$d = substr($_GET['date'],5,2);
							$y = substr($_GET['date'],0,4);
						}

						if (strlen($d)==1) {
							$d = "0".$d;
						}

						$month = $y."-".$d;

						$curdate = "date_format(curdate,'%d/%m/%Y')";
						$query = $db->prepare("SELECT DISTINCT $curdate,curdate FROM weather WHERE curdate LIKE '$month-%'");
						$query -> execute();
						//$result = $query -> fetch(PDO::FETCH_ASSOC);
						//echo $result[$curdate];
						//echo $result['curdate']; 
						

					?>
						
						<form action='chart.php' method='get'>

							<ul>
								<li>
									<?php
	
									
										echo "<select class='drop-menu' name='date' id='day'>";
										echo "<option disabled selected>ΗΜΕΡΟΜΗΝΙΑ</option>";
												
												while($result = $query -> fetch(PDO::FETCH_ASSOC)){
													if (isset($_GET['date']) && $_GET['date'] == $result['curdate']) {
														$selected = 'selected';
													}else{
														$selected = '';
													}

													echo "<option " . $selected . " value=" . $result['curdate'] .">" . $result[$curdate] . "</option>";
													
												}
									
										?>
									
									</select>
										
							</li>
							<li>
								<input id='submit2' value='ΕΠΙΛΟΓΗ' type='submit'> 
							</li>
						</ul>	
						</form>

					<?php	
						
						}

					?>
						
						<div id='fixed'>	
						
					</div>
					</div>

					<div id="right-list" class="lineplot">
							

						<?php
							if (isset($_GET['month'])) {
								echo 	"<span class='chart_h'>ΘΕΡΜΟΚΡΑΣΙΑ</span>";
								echo  	"<img class='img_c' src='month_t.php?month=".$_GET['month']."'/>";
								echo 	"<span class='chart_h'>ΣΧΕΤΙΚΗ ΥΓΡΑΣΙΑ</span>";
								echo    "<img class='img_c' src='month_h.php?month=".$_GET['month']."'/>";
								echo 	"<span class='chart_h'>ΑΜΟΣΦΑΙΡΙΚΗ ΠΙΕΣΗ</span>";
								echo    "<img class='img_c' src='month_a.php?month=".$_GET['month']."'/>";
							
							}	
							if (isset($_GET['date'])) {
								echo 	"<span class='chart_h'>ΘΕΡΜΟΚΡΑΣΙΑ</span>";
								echo  	"<img class='img_c' src='day_t.php?date=".$_GET['date']."'/>";
								echo 	"<span class='chart_h'>ΣΧΕΤΙΚΗ ΥΓΡΑΣΙΑ</span>";
								echo    "<img class='img_c' src='day_h.php?date=".$_GET['date']."'/>";
								echo 	"<span class='chart_h'>ΑΜΟΣΦΑΙΡΙΚΗ ΠΙΕΣΗ</span>";
								echo    "<img class='img_c' src='day_a.php?date=".$_GET['date']."'/>";
							}else{
							
							}			
						?>		
							 	
					</div>	
						<div id='measures' class='left_m'>
								<?php 
									if (isset($_SESSION['mint']) ) {
								//echo $min_temp;
								?>	
								<h3>ΘΕΡΜΟΤΗΤΑ</h3>
								<ul>
									<li>ΕΛΑΧΙΣΤΗ: <?php echo $_SESSION['mint'] ; ?>&degC</li>
									<li>ΜΕΣΗ: <?php echo $avg_temp; ?>&degC</li>
									<li>ΜΕΓΙΣΤΗ: <?php echo $max_temp; ?>&degC</li>
								</ul>
							</div>
							<div id='measures'>
								<h3>ΣΧΕΤΙΚΗ ΥΓΡΑΣΙΑ</h3>
								<ul>
									<li>ΕΛΑΧΙΣΤΗ: <?php echo $min_hum; ?> %</li>
									<li>ΜΕΣΗ: <?php echo $avg_hum; ?> %</li>
									<li>ΜΕΓΙΣΤΗ: <?php echo $max_hum; ?> %</li>
								</ul>
							</div>
							<div id='measures'>
								<h3>ΑΤΜΟΣΦΑΙΡΙΚΗ ΠΙΕΣΗ</h3>
								<ul>
									<li>ΕΛΑΧΙΣΤΗ: <?php echo $min_atm; ?> bns</li>
									<li>ΜΕΣΗ: <?php echo $avg_atm; ?> bns</li>
									<li>ΜΕΓΙΣΤΗ: <?php echo $max_atm; ?> bns</li>
								</ul>
							</div>
							<?php

								 } 
							?>
						</div>		
			</div>
		</div>
	</div>
</body>
</html>

<?php
	require_once('db/close.php');
?>