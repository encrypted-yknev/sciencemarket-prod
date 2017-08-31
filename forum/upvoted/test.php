<?php
	$time = '2017-07-31';
	$date = substr($time,8,2);
	$month = substr($time,5,2);
	$year = substr($time,0,4);
	
	echo 'Date - '.$date.'</br>';
	echo 'Month - '.$month.'</br>';
	echo 'Year - '.$year.'</br>';
	date_default_timezone_set("Asia/Kolkata");
	echo 'Current timestamp - '.date("Y-m-d h:i:sa").'</br>';
	echo date("Y").'</br>';
	echo date("m").'</br>';
	echo date("d").'</br>';
	echo date("h").'</br>';
	echo date("H:i").'</br>';
	/*echo date("Y");*/
?>