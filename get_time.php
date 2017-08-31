
<?php

session_start();

if(isset($_REQUEST['offset']))
	$timezone_offset_minutes = $_REQUEST['offset'];

$timezone_name = timezone_name_from_abbr("", $timezone_offset_minutes*60, false);
date_default_timezone_set($timezone_name);
echo $timezone_name.'</br>';

?>