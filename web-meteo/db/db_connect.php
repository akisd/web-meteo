<?php

$dsn = "mysql:host=127.0.0.1;dbname=project";
$username = "root";
$password = "";

try{

	$db = new PDO($dsn,$username,$password);
	$db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$db -> exec("SET NAMES UTF8");
	echo "ok";

}catch(PDOException $e){
	echo "ERROR: " . $e -> getMessage();
}
?>