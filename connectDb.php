<?php

#Connection paramters.

$username="root";
$password="";
$servername="localhost";

try	{
	$conn=new PDO("mysql:host=$servername;dbname=testdb",$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	#echo "Connection Succeeded";
}

catch(PDOException $e)	{
	echo "Error in connection: ".$e->getMessage();
}

?>