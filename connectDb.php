<?php

#Connection paramters.

$username="root";
$password="mysqlroot";
$servername="localhost";

try	{
	$conn=new PDO("mysql:host=$servername;dbname=testdb",$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e)	{
	echo "Error in connection. Please try after some time.";
}

?>
