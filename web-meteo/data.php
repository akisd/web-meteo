<?php
require_once('db/connect.php');

date_default_timezone_set("Europe/Athens");

try{

if(isset($_GET['temp']) && isset($_GET['hum']) && isset($_GET['atm'])){

	$query = $db -> prepare("INSERT INTO weather (curdate,curtime,temperature,humidity,atm_pressure) 
							 VALUES (CURDATE(),CURTIME(),:temp,:hum,:atm)");

	$query -> execute(array(':temp'=>$_GET['temp'],':hum'=>$_GET['hum'],':atm'=>$_GET['atm']));
}

}catch(PDOException $e){
	echo "ERROR: " . $e -> getMessage();
}
?>