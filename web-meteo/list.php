<?php
	require_once('db/connect.php');
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

				<form action='list.php' method='get'>
						<ul>
							<li>	
								
										<select class="drop-menu" id="month" name="month" onchange="this.form.submit()">

											<?php
													if (isset($_GET['month'])) {
														echo "<option disabled selected>" .$month_name[$_GET['month']-1] .
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
													
													echo "<option $selected value='$mon'>" . $month_name[$mon-1] . " " . $year . "</option>";
														
													}
											?>
										</select>
											
							</li>
						</ul>				
											
				</form>
				<form action='list.php' method='get'>

							<ul>
								<li>
									<?php
										if (!isset($_GET['month'])) {
											$disabled='disabled';
										}else{
											$disabled='';
										}
									
										echo "<select $disabled class='drop-menu' name='date' id='day'>";
										?>

										<?php 
											if(isset($_GET['month'])){
												$m = $_GET['month'];
											}else{
												$m ="";
											}
												echo "<option disabled selected>ΗΜΕΡΟΜΗΝΙΑ</option>";
												for ($j=0; $j < $count; $j++) { 
													$md = substr($date_1[$j],3,2);
													if ($md == $m){
														if (count($_GET) && $_GET['date']==$date_2[$j]) {
															$selected ='selected';
														}else{
															$selected = '';
														}

													echo "<option " . $selected . " value=" . $date_2[$j] .">" . $date_1[$j] . "</option>";
													}
												}
											
									
										?>
									
									</select>
										
							</li>
							<li>
								<input  id='submit' value='ΕΠΙΛΟΓΗ' type='submit'> 
							</li>
						</ul>	
						</form>
					
						
							
					</div>
					</div>

					<div id="right-list">
							<table id='results'>
							  <tr>
							    <th>ΗΜΕΡΟΜΗΝΙΑ</th>
							    <th>ΩΡΑ</th> 
							    <th>ΘΕΡΜΟΚΡΑΣΙΑ</th>
							    <th>ΣΧΕΤΙΚΗ ΥΓΡΑΣΙΑ</th>
							    <th>ΑΤΜΟΣΦΑΙΡΙΚΗ ΠΙΕΣΗ</th>
							  </tr>

							  <?php


								if (isset($_GET['date']) && !empty($_GET['date'])){
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
							  				$humi =  sprintf('%0.2f',$result['hum']);
							  				$atmo = sprintf('%0.2f',$result['atm']);

							  				
							  		$temp[$i] = $tempe;
							  		$hum[$i] = $humi;
							  		$atm[$i] = $atmo;


							  			
							  		if(strlen($start) == 7){
							  			$start = "0".$start;
							  		}	
							  		$start = substr($start,0,5);	

							  		 				  												  							  			
							  ?>

							  <tr>
							    <td><?php echo $result[$curdate]; ?></td>
							    <td><?php echo $start; ?></td> 
							    <td><?php echo $tempe; ?>&degC</td>
							    <td><?php echo $humi; ?> %</td> 
							    <td><?php echo $atmo; ?> hPa</td>
							  </tr>

							  <?php 
									  	}

							  		} 
							  	
							  	}
							  	
							  	if (isset($temp)) {
							  		$min_t = min($temp);
							  		$max_t = max($temp);
							  		$avg_t = array_sum($temp) / count($temp);
							  		$avg_t = sprintf('%0.2f',$avg_t);
							  		$min_h = min($hum);
							  		$max_h = max($hum);
							  		$avg_h = array_sum($hum) / count($hum);
							  		$avg_h = sprintf('%0.2f',$avg_h);
							  		$min_a = min($atm);
							  		$max_a = max($atm);
							  		$avg_a = array_sum($atm) / count($atm);
							  		$avg_a = sprintf('%0.2f',$avg_a);
							  			
							  	}			


							  ?>		
						</div>	
						<div id='fixed' class='left_m'>	
							<div id='measures'>
								<?php 
									if (isset($min_t)) {
								
								?>	
								<h3>ΘΕΡΜΟΚΡΑΣΙΑ</h3>
								<ul>
									<li>ΕΛΑΧΙΣΤΗ: <?php echo $min_t; ?>&degC</li>
									<li>ΜΕΣΗ: <?php echo $avg_t; ?>&degC</li>
									<li>ΜΕΓΙΣΤΗ: <?php echo $max_t; ?>&degC</li>
								</ul>
							</div>
							<div id='measures'>
								<h3>ΣΧΕΤΙΚΗ ΥΓΡΑΣΙΑ</h3>
								<ul>
									<li>ΕΛΑΧΙΣΤΗ: <?php echo $min_h; ?> %</li>
									<li>ΜΕΣΗ: <?php echo $avg_h; ?> %</li>
									<li>ΜΕΓΙΣΤΗ: <?php echo $max_h; ?> %</li>
								</ul>
							</div>
							<div id='measures'>
								<h3>ΑΤΜΟΣΦΑΙΡΙΚΗ ΠΙΕΣΗ</h3>
								<ul>
									<li>ΕΛΑΧΙΣΤΗ: <?php echo $min_a; ?> hPa</li>
									<li>ΜΕΣΗ: <?php echo $avg_a; ?> hPa</li>
									<li>ΜΕΓΙΣΤΗ: <?php echo $max_a; ?> hPa</li>
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