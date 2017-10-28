<?php
	require_once('db/connect.php');
	date_default_timezone_set("Europe/Athens");

	$url1=$_SERVER['REQUEST_URI'];
	header("Refresh: 10; URL=$url1");
?>


<!DOCTYPE html>
<html>
<head>
	<?php

$url1=$_SERVER['REQUEST_URI'];

//header("Refresh: 5; URL=$url1");

?>
	<meta charset="UTF-8">
	<title>arduino weather station</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link href="images/sun2.png" rel="icon" type="image/x-icon" />

		<script type="text/javascript">			//map script
			function geoFindMe() {
			  var output = document.getElementById("map");

			  if (!navigator.geolocation){
			    output.innerHTML = "<p>Geolocation is not supported by your browser</p>";
			    return;
			 }

			 function success(position) {
			    var latitude  = position.coords.latitude;
			    var longitude = position.coords.longitude;

			   // output.innerHTML = '<p>Latitude is ' + latitude + '° <br>Longitude is ' + longitude + '°</p>';

			    var img = new Image();
			    img.src = "http://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=15&size=385x290&sensor=false";

			    output.appendChild(img);
			 };

			  function error() {
			    output.innerHTML = "Unable to retrieve your location";
			  };

		  //output.innerHTML = "<p>Locating…</p>";

		  navigator.geolocation.getCurrentPosition(success, error);
		}
	</script>	

	<script>							// clock script
		function startTime() {
		    var today=new Date();
		    var h=today.getHours();
		    var m=today.getMinutes();
		    var s=today.getSeconds();
		    m = checkTime(m);
		    s = checkTime(s);
		    document.getElementById('clock').innerHTML = h+":"+m+":"+s;
		    var t = setTimeout(function(){startTime()},500);
		}

		function checkTime(i) {
		    if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
		    return i;
		}
	</script>
</head>

<body onload="geoFindMe(),startTime()">
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

<?php
	$hour = date('H');
	$minute = date('i');
	$second = date('s') - 20;
	if($second < 0 && $hour != 0){
		$minute = date('i')-1;
		$second = $second + 60;
		if($minute < 0){
			$minute = 59;
			$hour = $hour - 1;
		}else if($minute >= 0 && $minute < 10){
			$minute = "0" . $minute;
		}
	}else if($second > 0 && $second < 10){
		$second = "0".$second;
	}

	$time = (string)$hour.":".(string)$minute.":".(string)$second;
	$date = date('Y-m-d');
	$curdate = "date_format(curdate,'%d/%m/%Y')";

	$query2 = $db -> prepare("SELECT * FROM weather WHERE curdate = '$date' ORDER BY id desc limit 1");
	$query2 -> execute();
	$data = $query2 -> fetch(PDO::FETCH_ASSOC);
	$temp_d = sprintf('%0.2f',$data['temperature']);
	$hum_d = sprintf('%0.2f',$data['humidity']);
	$atm_d = sprintf('%0.2f',$data['atm_pressure']);

?>	
			<spam id='status'>status</spam>
<?php			
		if($data['curtime'] >= $time ){
			echo	"<img id='led' src='images/green.png'>";

			
		}else{
			echo "<img id='led' src='images/red.png'>";
			$temp_d = "-";
			$hum_d = "-";
			$atm_d = "-";
		}
?>		
			
				
			<ul id="timer">
				<li><?php echo date('d/m/Y'); ?></li>
				<li><img src="images/sun2.png"></li>
				<li id='clock'></li>
			</ul>
			<div id='left-container'>

<?php
	
	//	code for every 10 seconds measurements ? 


try{
	$query = $db -> prepare("SELECT MIN(temperature) AS mintemp, MAX(temperature) AS maxtemp, AVG(temperature) AS avgtemp, 
									MIN(humidity) AS minhum, MAX(humidity) AS maxhum, AVG(humidity) AS avghum,
									MIN(atm_pressure) AS minatm, MAX(atm_pressure) AS maxatm, AVG(atm_pressure) AS avgatm
							 FROM weather WHERE curdate = '$date' ");
	$query -> execute();
	$result = $query -> fetch(PDO::FETCH_ASSOC);
	$min_temp = round($result['mintemp'],2);
	$max_temp = round($result['maxtemp'],2);
	$avg_temp = round($result['avgtemp'],2);

	$min_hum = round($result['minhum'],2);
	$max_hum = round($result['maxhum'],2);
	$avg_hum = round($result['avghum'],2);

	$min_atm = round($result['minatm'],2);
	$max_atm = round($result['maxatm'],2);
	$avg_atm = round($result['avgatm'],2);

	$query2 = $db -> prepare("SELECT * FROM weather WHERE curdate = '$date' ORDER BY id desc limit 1");
	$query2 -> execute();
	$data = $query2 -> fetch(PDO::FETCH_ASSOC);
	

} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>

				<div id='left-block'>
					<h2>ΤΡΕΧΟΥΣΕΣ ΜΕΤΡΗΣΕΙΣ</h2>
					<ul>
						<li>ΘΕΡΜΟΚΡΑΣΙΑ:</li>
						<li>ΣΧΕΤΙΚΗ ΥΓΡΑΣΙΑ:</li>
						<li>ΑΤΜΟΣΦΑΙΡΙΚΗ ΠΙΕΣΗ:</li>
					</ul>
					<ul>
						<li><?php echo $temp_d; ?> &degC</li>
						<li><?php echo $hum_d; ?> %</li>
						<li><?php echo $atm_d;  ?> hPa	</li>
					</ul>
				</div>
				<div id='map'></div>	
			</div>
			<!--<div class="clear"></div>-->
			<div id='right-container'>
				<div id='right-block'>
					<h2>ΜΕΤΡΗΣΕΙΣ ΘΕΡΜΟΚΡΑΣΙΑΣ</h2>
					<ul>
						<li>ΕΛΑΧΙΣΤΗ:</li>
						<li>ΜΕΣΗ:</li>
						<li>ΜΕΓΙΣΤΗ:</li>
					</ul>
					<ul>
						<li><?php printf("%.2f",$min_temp); ?>&degC</li>
						<li><?php printf("%.2f",$avg_temp); ?>&degC</li>
						<li><?php printf("%.2f",$max_temp); ?>&degC</li>
					</ul>
				</div>
				<div id='right-block1'>
					<h2>ΜΕΤΡΗΣΕΙΣ ΣΧΕΤΙΚΗΣ ΥΓΡΑΣΙΑΣ</h2>
					<ul>
						<li>ΕΛΑΧΙΣΤΗ:</li>
						<li>ΜΕΣΗ:</li>
						<li>ΜΕΓΙΣΤΗ:</li>
					</ul>
					<ul>
						<li><?php printf("%.2f",$min_hum); ?> %</li>
						<li><?php printf("%.2f",$avg_hum); ?> %</li>
						<li><?php printf("%.2f",$max_hum); ?> %</li>
					</ul>
				</div>
				<div id='right-block2'>
					<h2>ΜΕΤΡΗΣΕΙΣ ΑΤΜ/ΚΗΣ ΠΙΕΣΗΣ</h2>
					<ul>
						<li>ΕΛΑΧΙΣΤΗ:</li>
						<li>ΜΕΣΗ:</li>
						<li>ΜΕΓΙΣΤΗ:</li>
					</ul>
					<ul>
						<li><?php printf("%.2f",$min_atm); ?> hPa	</li>
						<li><?php printf("%.2f",$avg_atm); ?> hPa	</li>
						<li><?php printf("%.2f",$max_atm); ?> hPa	</li>
					</ul>
				</div>
			</div>
		</div>	
	</div>
	<footer>
	</footer>

</body>
</html>

<?php
	require_once('db/close.php');
?>